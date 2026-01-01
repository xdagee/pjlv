<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Staff;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\StaffLeave;
use App\Models\LeaveAction;
use App\Enums\LeaveStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Staff $regularStaff;
    protected Department $department;
    protected LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['id' => 1, 'role_name' => 'Admin', 'role_status' => 1]);
        Role::create(['id' => 2, 'role_name' => 'Staff', 'role_status' => 1]);

        // Create admin user (ID 1 is Super Admin)
        $this->adminUser = User::factory()->create([
            'id' => 1,
            'email' => 'admin@example.com',
        ]);

        // Create admin profile for user ID 1
        Admin::create([
            'user_id' => 1,
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
        ]);

        // Create regular user
        $this->department = Department::create(['name' => 'IT', 'description' => 'Tech']);
        $this->leaveType = LeaveType::create(['leave_type_name' => 'Annual', 'leave_duration' => 20]);

        $this->regularStaff = Staff::factory()->create([
            'id' => 5,
            'department_id' => $this->department->id,
            'role_id' => 2,
        ]);

        $this->regularUser = User::factory()->create([
            'id' => 5,
            'email' => 'staff@example.com',
        ]);
    }

    protected function createApprovedLeave(): StaffLeave
    {
        $leave = StaffLeave::create([
            'staff_id' => $this->regularStaff->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-05',
            'leave_days' => 5,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $this->regularStaff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
            'action_reason' => 'Approved',
        ]);

        return $leave;
    }

    public function test_admin_can_access_analytics_dashboard(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics');

        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics.index');
        $response->assertViewHas('stats');
        $response->assertViewHas('leavesByDepartment');
        $response->assertViewHas('leavesByType');
        $response->assertViewHas('monthlyTrends');
    }

    public function test_non_admin_cannot_access_analytics_dashboard(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/analytics');

        $response->assertRedirect('/dashboard');
    }

    public function test_unauthenticated_user_cannot_access_analytics(): void
    {
        $response = $this->get('/admin/analytics');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_export_csv(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics/export?year=2025');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="leave_analytics_2025.csv"');
    }

    public function test_csv_contains_correct_headers(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics/export?year=2025');

        $content = $response->streamedContent();

        // Check for section headers
        $this->assertStringContainsString('OVERVIEW STATISTICS', $content);
        $this->assertStringContainsString('LEAVES BY DEPARTMENT', $content);
        $this->assertStringContainsString('LEAVES BY TYPE', $content);
        $this->assertStringContainsString('MONTHLY TRENDS', $content);
        $this->assertStringContainsString('TOP LEAVE TAKERS', $content);
    }

    public function test_csv_export_with_month_filter(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics/export?year=2025&month=3');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="leave_analytics_2025_March.csv"');
    }

    public function test_admin_can_export_pdf(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics/export-pdf?year=2025');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_non_admin_cannot_export_csv(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/analytics/export?year=2025');

        $response->assertRedirect('/dashboard');
    }

    public function test_non_admin_cannot_export_pdf(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/analytics/export-pdf?year=2025');

        $response->assertRedirect('/dashboard');
    }

    public function test_filters_affect_results(): void
    {
        // Create leave in March 2025
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        // Request for June (should have no data for department)
        $response = $this->get('/admin/analytics?year=2025&month=6');

        $response->assertStatus(200);
        $response->assertViewHas('leavesByDepartment', function ($collection) {
            return $collection->isEmpty();
        });

        // Request for March (should have data)
        $response = $this->get('/admin/analytics?year=2025&month=3');

        $response->assertStatus(200);
        $response->assertViewHas('leavesByDepartment', function ($collection) {
            return $collection->isNotEmpty();
        });
    }

    public function test_year_filter_works_correctly(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        // Request for different year (should have no data)
        $response = $this->get('/admin/analytics?year=2024');

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_requests'] === 0;
        });

        // Request for 2025 (should have data)
        $response = $this->get('/admin/analytics?year=2025');

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_requests'] > 0;
        });
    }

    public function test_dashboard_displays_correct_statistics(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics?year=2025');

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['approved'] === 1 &&
                $stats['total_days'] === 5 &&
                $stats['approval_rate'] === 100.0;
        });
    }

    public function test_dashboard_view_contains_charts(): void
    {
        $this->createApprovedLeave();

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics?year=2025');

        $response->assertStatus(200);
        $response->assertSee('monthlyChart');
        $response->assertSee('yearlyChart');
        $response->assertSee('departmentPieChart');
        $response->assertSee('leaveTypePieChart');
    }

    public function test_dashboard_shows_export_buttons(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/analytics');

        $response->assertStatus(200);
        $response->assertSee('Export CSV');
        $response->assertSee('Export PDF');
    }
}
