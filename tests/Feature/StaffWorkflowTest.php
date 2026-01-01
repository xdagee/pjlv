<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\LeaveLevel;
use App\Enums\RoleEnum;
use App\Enums\LeaveStatusEnum;
use App\Models\LeaveType;
use App\Models\StaffLeave;
use App\Models\LeaveAction;

class StaffWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles required for the test
        Role::unguard();
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(
                ['id' => $role->value],
                ['role_name' => $role->label(), 'role_status' => 1]
            );
        }
        Role::reguard();
    }

    public function test_hr_can_create_staff_and_system_assigns_defaults()
    {
        // 1. Setup: Create HR User to act as
        // We need an existing user with Role ID 2 (HR)
        $hrUser = User::factory()->create();
        $hrStaff = \App\Models\Staff::factory()->create([
            'id' => $hrUser->id,
            'role_id' => RoleEnum::HR->value,
            // 'email' => $hrUser->email // Staff table has no email
        ]);

        $hrUser->refresh();
        $this->actingAs($hrUser);

        // 2. Data for new staff (Management Level -> Should be 36 days)
        // We do NOT send 'total_leave_days' to test auto-calculation
        $managementLevel = LeaveLevel::where('level_name', 'Management')->first();
        // If no seeds, create one for test validity
        if (!$managementLevel) {
            $managementLevel = LeaveLevel::create(['level_name' => 'Management', 'annual_leave_days' => 36]);
        }

        $staffData = [
            'firstname' => 'Eve',
            'lastname' => 'Manager',
            'title' => 'Ms',
            'dob' => '1990-01-01',
            'mobile_number' => '0555555555',
            'gender' => 1,
            'date_joined' => '2023-01-01',
            'role_id' => RoleEnum::HOD->value, // Head of Dept
            'leave_level_id' => $managementLevel->id,
            'department_id' => Department::first()->id ?? Department::create(['name' => 'IT'])->id,
            // 'total_leave_days' => 36, // INTENTIONALLY OMITTED
            'email' => 'eve.manager@pjlv.test', // Email for User account
            'password' => 'password', // Password for User account
        ];

        // 3. Action: Post to HR route
        $response = $this->post('/staff', $staffData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/staff');

        // 4. Assertions
        $response->assertStatus(302);

        // A. Verify Staff Record
        $this->assertDatabaseHas('staff', [
            'firstname' => 'Eve',
            'lastname' => 'Manager',
            'role_id' => RoleEnum::HOD->value,
            'total_leave_days' => 36, // Should match Management level
        ]);

        // B. Verify User Account Created (Critical for Workflow)
        $this->assertDatabaseHas('users', [
            'email' => 'eve.manager@pjlv.test',
        ]);

        // Clean up
        $user = User::where('email', 'eve.manager@pjlv.test')->first();
        if ($user) {
            $user->staff()->delete();
            $user->delete();
        }
    }

    public function test_staff_leave_workflow_approval_chain()
    {
        // 1. Setup Actors
        // Eve (Normal)
        $eveUser = User::factory()->create();
        $eveStaff = \App\Models\Staff::factory()->create([
            'id' => $eveUser->id,
            'role_id' => RoleEnum::NORMAL->value,
            'firstname' => 'Eve',
            'lastname' => 'Staff'
        ]);

        // David (HOD)
        $davidUser = User::factory()->create();
        $davidStaff = \App\Models\Staff::factory()->create([
            'id' => $davidUser->id,
            'role_id' => RoleEnum::HOD->value,
            'firstname' => 'David',
            'lastname' => 'Head'
        ]);

        // Alice (HR)
        $aliceUser = User::factory()->create();
        $aliceStaff = \App\Models\Staff::factory()->create([
            'id' => $aliceUser->id,
            'role_id' => RoleEnum::HR->value,
            'firstname' => 'Alice',
            'lastname' => 'HR'
        ]);

        // Bob (CEO)
        $bobUser = User::factory()->create();
        $bobStaff = \App\Models\Staff::factory()->create([
            'id' => $bobUser->id,
            'role_id' => RoleEnum::CEO->value,
            'firstname' => 'Bob',
            'lastname' => 'CEO'
        ]);

        // Refresh users to load relationships correctly (avoid earlier bug)
        $eveUser->refresh();
        $davidUser->refresh();
        $aliceUser->refresh();
        $bobUser->refresh();

        $this->withoutExceptionHandling();

        // 2. Setup Data
        $leaveType = LeaveType::create(['leave_type_name' => 'Annual Leave', 'leave_duration' => 30]);

        // 3. Eve Applies for Leave
        $this->actingAs($eveUser);
        $response = $this->post('/leaves', [
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'leave_days' => 5, // Frontend calculates this
            'reason' => 'Vacation'
        ]);
        $response->assertRedirect('/leaves');

        $leave = StaffLeave::where('staff_id', $eveStaff->id)->first();
        $this->assertNotNull($leave);
        // Verify initial status (UNATTENDED)
        $this->assertEquals(LeaveStatusEnum::UNATTENDED->value, $leave->leaveAction->last()->status_id);

        // 4. David (HOD) Recommends
        $this->actingAs($davidUser);
        $response = $this->put("/leaves/{$leave->id}", [
            'action' => 'recommend',
            'reason' => 'Recommended by HOD'
        ]);
        $response->assertRedirect("/leaves/{$leave->id}");

        $leave->refresh(); // Reload relations
        $actions = $leave->leaveAction()->orderBy('id', 'asc')->get();

        $this->assertEquals(LeaveStatusEnum::RECOMMENDED->value, $actions->last()->status_id);

        // 5. Alice (HR) Recommends (to CEO)
        $this->actingAs($aliceUser);
        $response = $this->put("/leaves/{$leave->id}", [
            'action' => 'recommend',
            'reason' => 'Verified by HR'
        ]);
        $response->assertRedirect("/leaves/{$leave->id}");

        $leave->refresh();
        $this->assertEquals(LeaveStatusEnum::RECOMMENDED->value, $leave->leaveAction()->latest()->first()->status_id);

        // 6. Bob (CEO) Approves
        $this->actingAs($bobUser);
        $response = $this->put("/leaves/{$leave->id}", [
            'action' => 'approve',
            'reason' => 'Final Approval'
        ]);
        $response->assertRedirect("/leaves/{$leave->id}");

        $leave->refresh();
        $this->assertEquals(LeaveStatusEnum::APPROVED->value, $leave->leaveAction()->latest()->first()->status_id);
    }
}
