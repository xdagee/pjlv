<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Staff;
use App\Models\LeaveType;
use App\Models\StaffLeave;
use App\Models\LeaveAction;
use App\Enums\LeaveStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Services\LeaveBalanceService;

class ComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed necessary data
        $this->seed();
    }

    /** @test */
    public function system_owner_admin_has_unrestricted_access_via_id_1()
    {
        // Ensure User ID 1 exists as System Owner
        $admin = User::find(1);
        if (!$admin) {
            $admin = User::create([
                'id' => 1,
                'email' => 'admin@system.com',
                'password' => Hash::make('password'),
            ]);
        }

        // This user has NO staff record attached (simulating "Admin is NOT a staff member")

        $response = $this->actingAs($admin)
            ->get('/admin/dashboard');

        // Should allow access (200) instead of redirect (302)
        $response->assertStatus(200);
    }

    /** @test */
    public function pending_leaves_deduct_from_balance()
    {
        $service = new LeaveBalanceService();

        // Create Staff
        $staff = Staff::factory()->create([
            'total_leave_days' => 20
        ]);

        // Create Leave Type
        $type = LeaveType::create([
            'leave_type_name' => 'Test Leave',
            'leave_duration' => 5,
        ]);

        // Create a PENDING (Unattended) Leave Request for 5 days
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $type->id,
            'start_date' => now(),
            'end_date' => now()->addDays(4),
            'leave_days' => 5
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
            'actionby' => $staff->id
        ]);

        // Check Balance
        // Total 20 - Used 5 = Remaining 15
        $balance = $service->getBalance($staff->id);

        $this->assertEquals(15, $balance, "Pending leaves must temporarily deduct from balance.");
    }

    /** @test */
    public function roles_auto_activate_and_deactivate()
    {
        // Create a new Inactive Role
        $role = Role::create([
            'role_name' => 'Auto Test Role',
            'role_status' => 0
        ]);

        $this->assertEquals(0, $role->fresh()->role_status, "Role should start Inactive.");

        // Assign to Staff
        $staff = Staff::factory()->create();
        $originalRoleID = $staff->role_id; // Factory assigns a random or default role

        $staff->role_id = $role->id;
        $staff->save();

        // Check Observer Activation
        $this->assertEquals(1, $role->fresh()->role_status, "Role should activate when assigned to staff.");

        // Revert to original role (simulating moving staff away)
        $staff->role_id = $originalRoleID;
        $staff->save();

        // Check Observer Deactivation
        $this->assertEquals(0, $role->fresh()->role_status, "Role should deactivate when no staff assigned.");
    }
}
