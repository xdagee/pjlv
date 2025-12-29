<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\StaffLeave;
use App\Staff;
use App\LeaveType;

class StaffLeaveTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a leave request can be created.
     */
    public function test_leave_request_can_be_created(): void
    {
        $staff = Staff::first();
        $leaveType = LeaveType::first();

        if ($staff && $leaveType) {
            $leave = StaffLeave::create([
                'start_date' => '2025-03-01',
                'end_date' => '2025-03-05',
                'leave_days' => 5,
                'leave_type_id' => $leaveType->id,
                'staff_id' => $staff->id,
            ]);

            $this->assertNotNull($leave->id);
            $this->assertEquals(5, $leave->leave_days);
        } else {
            $this->markTestSkipped('No staff or leave type found');
        }
    }

    /**
     * Test leave request belongs to staff.
     */
    public function test_leave_request_belongs_to_staff(): void
    {
        $leave = StaffLeave::first();

        if ($leave) {
            $this->assertInstanceOf(Staff::class, $leave->staff);
        } else {
            $this->markTestSkipped('No leave request found');
        }
    }

    /**
     * Test leave request belongs to leave type.
     */
    public function test_leave_request_belongs_to_leave_type(): void
    {
        $leave = StaffLeave::first();

        if ($leave) {
            $this->assertInstanceOf(LeaveType::class, $leave->leaveType);
        } else {
            $this->markTestSkipped('No leave request found');
        }
    }
}
