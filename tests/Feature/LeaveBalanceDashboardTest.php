<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Staff;
use App\Models\User;
use App\Models\StaffLeave;
use App\Models\LeaveAction;
use App\Enums\LeaveStatusEnum;
use Carbon\Carbon;

class LeaveBalanceDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveTypesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveStatusesTableSeeder::class);
    }

    /**
     * Test that unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/leave-balance');

        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated employee can access leave balance dashboard.
     */
    public function test_authenticated_employee_can_access_dashboard(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);
        $user = User::factory()->create(['id' => $staff->id]);

        $response = $this->actingAs($user)->get('/leave-balance');

        $response->assertStatus(200);
        $response->assertViewIs('leave-balance.index');
    }

    /**
     * Test that dashboard displays correct balance data.
     */
    public function test_dashboard_displays_correct_balance_data(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);
        $user = User::factory()->create(['id' => $staff->id]);

        // Create an approved leave of 5 days
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(5),
            'leave_days' => 5,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
        ]);

        $response = $this->actingAs($user)->get('/leave-balance');

        $response->assertStatus(200);
        // Check view has correct balance breakdown
        $response->assertViewHas('balanceBreakdown', function ($breakdown) {
            return $breakdown['total_allowance'] === 21
                && $breakdown['total_used'] === 5
                && $breakdown['remaining'] === 16;
        });
    }

    /**
     * Test that balance breakdown includes allocated per type.
     */
    public function test_dashboard_includes_allocated_per_leave_type(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);
        $user = User::factory()->create(['id' => $staff->id]);

        $response = $this->actingAs($user)->get('/leave-balance');

        $response->assertStatus(200);
        $response->assertViewHas('balanceBreakdown', function ($breakdown) {
            // Check that by_type contains allocated field
            foreach ($breakdown['by_type'] as $typeName => $data) {
                if (!array_key_exists('allocated', $data)) {
                    return false;
                }
                if (!array_key_exists('remaining', $data)) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Test dashboard shows staff information.
     */
    public function test_dashboard_provides_staff_data(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);
        $user = User::factory()->create(['id' => $staff->id]);

        $response = $this->actingAs($user)->get('/leave-balance');

        $response->assertStatus(200);
        $response->assertViewHas('staff');
    }

    /**
     * Test user without staff profile is redirected.
     */
    public function test_user_without_staff_profile_is_redirected(): void
    {
        // Create user without staff profile
        $user = User::factory()->create(['id' => 9999]);

        $response = $this->actingAs($user)->get('/leave-balance');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'No staff profile found.');
    }

    /**
     * Test dashboard updates after leave approval.
     */
    public function test_balance_updates_after_leave_approval(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);
        $user = User::factory()->create(['id' => $staff->id]);

        // First check - no leaves
        $response = $this->actingAs($user)->get('/leave-balance');
        $response->assertViewHas('balanceBreakdown', function ($breakdown) {
            return $breakdown['total_used'] === 0 && $breakdown['remaining'] === 21;
        });

        // Create and approve a leave
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addDays(13),
            'leave_days' => 4,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
        ]);

        // Clear cache to ensure fresh data
        \Illuminate\Support\Facades\Cache::flush();

        // Second check - with approved leave
        $response = $this->actingAs($user)->get('/leave-balance');
        $response->assertViewHas('balanceBreakdown', function ($breakdown) {
            return $breakdown['total_used'] === 4 && $breakdown['remaining'] === 17;
        });
    }

    /**
     * Test that the named route works correctly.
     */
    public function test_named_route_resolves_correctly(): void
    {
        $url = route('leave-balance.index');

        $this->assertEquals(url('/leave-balance'), $url);
    }
}
