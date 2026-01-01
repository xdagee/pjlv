<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Policies\StaffLeavePolicy;
use App\Models\User;
use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\LeaveAction;
use App\Enums\RoleEnum;
use App\Enums\LeaveStatusEnum;
use Carbon\Carbon;

class StaffLeavePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected StaffLeavePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new StaffLeavePolicy();
        $this->seed(\Database\Seeders\RolesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveTypesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveStatusesTableSeeder::class);
    }

    /**
     * Test owner can view their own leave request.
     */
    public function test_owner_can_view_own_leave(): void
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->create(['id' => $staff->id]);

        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        $this->assertTrue($this->policy->view($user, $leave));
    }

    /**
     * Test HR can view any leave request.
     */
    public function test_hr_can_view_any_leave(): void
    {
        $hrStaff = Staff::factory()->hr()->create();
        $hrUser = User::factory()->create(['id' => $hrStaff->id]);

        $otherStaff = Staff::factory()->create();
        $leave = StaffLeave::create([
            'staff_id' => $otherStaff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        $this->assertTrue($this->policy->view($hrUser, $leave));
    }

    /**
     * Test normal user cannot view others' leaves.
     */
    public function test_normal_user_cannot_view_others_leaves(): void
    {
        $staff = Staff::factory()->create(['role_id' => RoleEnum::NORMAL->value]);
        $user = User::factory()->create(['id' => $staff->id]);

        $otherStaff = Staff::factory()->create();
        $leave = StaffLeave::create([
            'staff_id' => $otherStaff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        $this->assertFalse($this->policy->view($user, $leave));
    }

    /**
     * Test owner can cancel pending leave.
     */
    public function test_owner_can_cancel_pending_leave(): void
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->create(['id' => $staff->id]);

        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
        ]);

        $this->assertTrue($this->policy->cancel($user, $leave));
    }

    /**
     * Test owner cannot cancel approved leave.
     */
    public function test_owner_cannot_cancel_approved_leave(): void
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->create(['id' => $staff->id]);

        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
        ]);

        $this->assertFalse($this->policy->cancel($user, $leave));
    }

    /**
     * Test HR can approve leaves.
     */
    public function test_hr_can_approve_pending_leave(): void
    {
        $hrStaff = Staff::factory()->hr()->create();
        $hrUser = User::factory()->create(['id' => $hrStaff->id]);

        $otherStaff = Staff::factory()->create();
        $leave = StaffLeave::create([
            'staff_id' => $otherStaff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $otherStaff->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
        ]);

        $this->assertTrue($this->policy->approve($hrUser, $leave));
    }

    /**
     * Test user cannot approve own leave.
     */
    public function test_user_cannot_approve_own_leave(): void
    {
        $staff = Staff::factory()->hr()->create();
        $user = User::factory()->create(['id' => $staff->id]);

        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
        ]);

        $this->assertFalse($this->policy->approve($user, $leave));
    }

    /**
     * Test admin can delete any leave.
     */
    public function test_admin_can_delete_any_leave(): void
    {
        $adminStaff = Staff::factory()->admin()->create();
        $adminUser = User::factory()->create(['id' => $adminStaff->id]);

        $otherStaff = Staff::factory()->create();
        $leave = StaffLeave::create([
            'staff_id' => $otherStaff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        $this->assertTrue($this->policy->delete($adminUser, $leave));
    }

    /**
     * Test normal user can only delete own pending leave.
     */
    public function test_normal_user_can_delete_own_pending_leave(): void
    {
        $staff = Staff::factory()->create(['role_id' => RoleEnum::NORMAL->value]);
        $user = User::factory()->create(['id' => $staff->id]);

        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
        ]);

        $this->assertTrue($this->policy->delete($user, $leave));
    }
}
