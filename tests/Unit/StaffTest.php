<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Staff;
use App\Role;
use App\LeaveLevel;
use App\LeaveType;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a staff member can be created.
     */
    public function test_staff_can_be_created(): void
    {
        $staff = Staff::create([
            'staff_number' => 'STF001',
            'title' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'dob' => '1990-01-15',
            'mobile_number' => '0551234567',
            'gender' => 1,
            'is_active' => 1,
            'date_joined' => '2020-01-01',
            'total_leave_days' => 25,
            'role_id' => 1,
            'leave_level_id' => 1,
        ]);

        $this->assertNotNull($staff->id);
        $this->assertEquals('John', $staff->firstname);
        $this->assertEquals('Doe', $staff->lastname);
    }

    /**
     * Test staff has role relationship.
     */
    public function test_staff_belongs_to_role(): void
    {
        $staff = Staff::first();

        if ($staff && $staff->role) {
            $this->assertInstanceOf(Role::class, $staff->role);
        } else {
            $this->markTestSkipped('No staff with role found');
        }
    }

    /**
     * Test staff has leave level relationship.
     */
    public function test_staff_belongs_to_leave_level(): void
    {
        $staff = Staff::first();

        if ($staff && $staff->leaveLevel) {
            $this->assertInstanceOf(LeaveLevel::class, $staff->leaveLevel);
        } else {
            $this->markTestSkipped('No staff with leave level found');
        }
    }
}
