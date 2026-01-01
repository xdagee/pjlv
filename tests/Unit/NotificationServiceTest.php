<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Models\Staff;
use App\Models\User;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Models\Role;
use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestApproved;
use App\Enums\RoleEnum;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService();
    }

    public function test_notify_supervisor_sends_email_and_notification()
    {
        Mail::fake();

        // Create Supervisor
        $supervisor = Staff::factory()->create();
        $supervisorUser = User::factory()->create(['email' => 'supervisor@example.com']);
        $supervisor->user()->save($supervisorUser); // Link user

        // Create Staff under Supervisor
        $staff = Staff::factory()->create(['supervisor_id' => $supervisor->id]);
        $staffUser = User::factory()->create(['email' => 'staff@example.com']);
        $staff->user()->save($staffUser);

        // Create Leave Request
        $leaveType = LeaveType::create(['leave_type_name' => 'Annual', 'description' => 'Test', 'leave_duration' => 20]);
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'leave_days' => 3,
        ]);

        $this->service->notifySupervisor($leave);

        // Assert Email Sent
        Mail::assertQueued(LeaveRequestSubmitted::class, function ($mail) use ($supervisorUser) {
            return $mail->hasTo('supervisor@example.com');
        });

        // Assert Database Notification Created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $supervisorUser->id,
            'title' => 'New Leave Request',
        ]);
    }

    public function test_notify_staff_sends_approved_email_and_notification()
    {
        Mail::fake();

        $staff = Staff::factory()->create();
        $user = User::factory()->create(['email' => 'staff@example.com']);
        $staff->user()->save($user);

        $leaveType = LeaveType::create(['leave_type_name' => 'Sick', 'description' => 'Test', 'leave_duration' => 20]);
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'leave_days' => 3,
        ]);

        // Create Approver (Admin/HR)
        $approver = Staff::factory()->create();

        // Create Leave Action (Approval)
        \App\Models\LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $approver->id,
            'status_id' => \App\Enums\LeaveStatusEnum::APPROVED->value,
            'action_reason' => 'Enjoy',
            'created_at' => now()->addSecond(),
        ]);

        $this->service->notifyStaff($leave, 'Approved');

        Mail::assertQueued(LeaveRequestApproved::class, function ($mail) use ($user) {
            return $mail->hasTo('staff@example.com');
        });

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => 'Leave Request Update',
            'type' => 'success',
        ]);
    }

    public function test_notify_leave_cancelled_notifies_supervisor_and_hr()
    {
        // Create HR
        $hrRole = Role::create(['id' => RoleEnum::HR->value, 'role_name' => 'HR', 'role_status' => 1]);
        $hr = Staff::factory()->create(['role_id' => RoleEnum::HR->value, 'is_active' => 1]);
        $hrUser = User::factory()->create(['email' => 'hr@example.com']);
        $hr->user()->save($hrUser);

        // Create Supervisor (different from HR)
        $normalRole = Role::firstOrCreate(['id' => 6], ['role_name' => 'Normal', 'role_status' => 1]);
        $supervisor = Staff::factory()->create(['role_id' => 6]);
        $supervisorUser = User::factory()->create(['email' => 'supervisor@example.com']);
        $supervisor->user()->save($supervisorUser);

        // Create Staff under Supervisor
        $staff = Staff::factory()->create(['supervisor_id' => $supervisor->id]);
        $staffUser = User::factory()->create(['email' => 'staff@example.com']);
        $staff->user()->save($staffUser);

        // Create Leave Request
        $leaveType = LeaveType::create(['leave_type_name' => 'Annual', 'description' => 'Test', 'leave_duration' => 20]);
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'leave_days' => 3,
        ]);

        $this->service->notifyLeaveCancelled($leave);

        // Assert Supervisor Notified
        $this->assertDatabaseHas('notifications', [
            'user_id' => $supervisorUser->id,
            'title' => 'Leave Request Cancelled',
        ]);

        // Assert HR Notified
        $this->assertDatabaseHas('notifications', [
            'user_id' => $hrUser->id,
            'title' => 'Leave Request Cancelled',
        ]);
    }

    public function test_notify_admins_and_managers_sends_to_all()
    {
        // Create Admin
        $adminRole = Role::firstOrCreate(['id' => RoleEnum::ADMIN->value], ['role_name' => 'Admin', 'role_status' => 1]);
        $admin = Staff::factory()->create(['role_id' => RoleEnum::ADMIN->value, 'is_active' => 1]);
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $admin->user()->save($adminUser);

        // Create HR
        $hrRole = Role::firstOrCreate(['id' => RoleEnum::HR->value], ['role_name' => 'HR', 'role_status' => 1]);
        $hr = Staff::factory()->create(['role_id' => RoleEnum::HR->value, 'is_active' => 1]);
        $hrUser = User::factory()->create(['email' => 'hr@example.com']);
        $hr->user()->save($hrUser);

        // Create Staff with Leave
        $staff = Staff::factory()->create();
        $leaveType = LeaveType::create(['leave_type_name' => 'Sick', 'description' => 'Test', 'leave_duration' => 20]);
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now(),
            'end_date' => now()->addDays(1),
            'leave_days' => 2,
        ]);

        $this->service->notifyAdminsAndManagers($leave, 'Test Title', 'Test message');

        // Assert Admin Notified
        $this->assertDatabaseHas('notifications', [
            'user_id' => $adminUser->id,
            'title' => 'Test Title',
        ]);

        // Assert HR Notified
        $this->assertDatabaseHas('notifications', [
            'user_id' => $hrUser->id,
            'title' => 'Test Title',
        ]);
    }
}

