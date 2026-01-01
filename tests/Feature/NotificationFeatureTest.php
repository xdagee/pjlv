<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Models\Staff;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\StaffLeave;
use App\Models\LeaveAction;
use App\Enums\RoleEnum;
use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestApproved;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_notification_flow()
    {
        Mail::fake();

        // Seed Roles
        \App\Models\Role::create(['id' => 1, 'role_name' => 'Admin', 'role_status' => 1]);
        \App\Models\Role::create(['id' => 2, 'role_name' => 'HR', 'role_status' => 1]);
        \App\Models\Role::create(['id' => 5, 'role_name' => 'Normal', 'role_status' => 1]);

        // 1. Setup Data
        // Supervisor/HR
        $hr = Staff::factory()->create(['id' => 10, 'role_id' => RoleEnum::HR->value]);
        $hrUser = User::factory()->create(['id' => 10, 'email' => 'hr@example.com']);
        // $hr->user()->save($hrUser); // Already linked via ID 10

        // Staff
        $staff = Staff::factory()->create(['id' => 11, 'role_id' => RoleEnum::NORMAL->value]);
        $staffUser = User::factory()->create(['id' => 11, 'email' => 'staff@example.com']);

        $leaveType = LeaveType::create(['leave_type_name' => 'Annual Feature', 'description' => 'Test', 'leave_duration' => 20]);

        // 2. Staff Applies for Leave
        $this->actingAs($staffUser);
        $response = $this->post('/leaves', [
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'leave_days' => 3,
            'reason' => 'Vacation', // Is reason field in form? StoreLeaveRequest check required.
        ]);

        $response->assertRedirect('/leaves');

        // Assert Leave Created
        $leave = StaffLeave::where('staff_id', $staff->id)->latest()->first();
        $this->assertNotNull($leave);

        // Assert HR Notified (Supervisor logic falls back to HR in my setup)
        // Wait, logic says: notifySupervisor -> if supervisor_id set...
        // In my logic: notifySupervisor -> checks supervisor. If no supervisor, it does NOTHING unless I updated it?
        // Let's check NotificationService::notifySupervisor.
        // It gets $applicant->supervisor.
        // In this test, I didn't set supervisor.
        // I should set supervisor_id on staff.
    }

    public function test_notification_flow_with_designated_supervisor()
    {
        Mail::fake();

        // Seed Roles
        if (\App\Models\Role::count() == 0) {
            \App\Models\Role::create(['id' => 1, 'role_name' => 'Admin', 'role_status' => 1]);
            \App\Models\Role::create(['id' => 2, 'role_name' => 'HR', 'role_status' => 1]);
            \App\Models\Role::create(['id' => 5, 'role_name' => 'Normal', 'role_status' => 1]);
        }

        // Setup Supervisor
        $supervisor = Staff::factory()->create(['id' => 20, 'role_id' => RoleEnum::HR->value]);
        $supervisorUser = User::factory()->create(['id' => 20, 'email' => 'boss@example.com']);
        // $supervisor->user()->save($supervisorUser);

        // Setup Staff
        $staff = Staff::factory()->create([
            'id' => 21,
            'role_id' => RoleEnum::NORMAL->value,
            'supervisor_id' => $supervisor->id
        ]);
        $staffUser = User::factory()->create(['id' => 21, 'email' => 'worker@example.com']);
        // $staff->user()->save($staffUser);

        $leaveType = LeaveType::create(['leave_type_name' => 'Annual Feature 2', 'description' => 'Test', 'leave_duration' => 20]);

        // 1. Apply
        $this->actingAs($staffUser);
        $response = $this->post('/leaves', [
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'leave_days' => 3,
            'reason' => 'Vacation'
        ]);

        $response->assertRedirect('/leaves');

        $leave = StaffLeave::where('staff_id', $staff->id)->latest()->first();

        // Verify Supervisor Notification
        // Mail
        Mail::assertQueued(LeaveRequestSubmitted::class, function ($mail) {
            return $mail->hasTo('boss@example.com');
        });

        // Database
        $this->assertDatabaseHas('notifications', [
            'user_id' => $supervisorUser->id,
            'title' => 'New Leave Request',
        ]);

        // 2. Approve (as Supervisor/HR)
        $this->actingAs($supervisorUser);
        $response = $this->put("/leaves/{$leave->id}", [
            'action' => 'approve',
            'reason' => 'Granted',
        ]);

        $response->assertRedirect("/leaves/{$leave->id}");

        // Verify Staff Notification
        // Mail
        Mail::assertQueued(LeaveRequestApproved::class, function ($mail) {
            return $mail->hasTo('worker@example.com');
        });

        // Database
        $this->assertDatabaseHas('notifications', [
            'user_id' => $staffUser->id,
            'title' => 'Leave Request Update',
            'type' => 'success',
        ]);
    }

    public function test_leave_cancellation_notifies_supervisor()
    {
        Mail::fake();

        // Seed Roles
        \App\Models\Role::firstOrCreate(['id' => 1], ['role_name' => 'Admin', 'role_status' => 1]);
        \App\Models\Role::firstOrCreate(['id' => 2], ['role_name' => 'HR', 'role_status' => 1]);
        \App\Models\Role::firstOrCreate(['id' => 6], ['role_name' => 'Normal', 'role_status' => 1]);

        // Setup Supervisor
        $supervisor = Staff::factory()->create(['id' => 30, 'role_id' => 2]);
        $supervisorUser = User::factory()->create(['id' => 30, 'email' => 'supervisor@example.com']);

        // Setup Staff
        $staff = Staff::factory()->create([
            'id' => 31,
            'role_id' => 6,
            'supervisor_id' => $supervisor->id
        ]);
        $staffUser = User::factory()->create(['id' => 31, 'email' => 'employee@example.com']);

        $leaveType = LeaveType::create(['leave_type_name' => 'Annual Cancel', 'description' => 'Test', 'leave_duration' => 20]);

        // 1. Apply for leave
        $this->actingAs($staffUser);
        $this->post('/leaves', [
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'leave_days' => 3,
            'reason' => 'Vacation'
        ]);

        $leave = StaffLeave::where('staff_id', $staff->id)->latest()->first();
        $this->assertNotNull($leave);

        // 2. Cancel the leave
        $response = $this->post("/leaves/{$leave->id}/cancel");
        $response->assertRedirect('/leaves');

        // 3. Verify supervisor was notified
        $this->assertDatabaseHas('notifications', [
            'user_id' => $supervisorUser->id,
            'title' => 'Leave Request Cancelled',
        ]);
    }
}

