<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AnalyticsService;
use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\LeaveAction;
use App\Models\Role;
use App\Enums\LeaveStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnalyticsService $analyticsService;
    protected Department $department1;
    protected Department $department2;
    protected LeaveType $leaveType1;
    protected LeaveType $leaveType2;
    protected Staff $staff1;
    protected Staff $staff2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyticsService = new AnalyticsService();

        // Create roles
        Role::create(['id' => 1, 'role_name' => 'Admin', 'role_status' => 1]);
        Role::create(['id' => 2, 'role_name' => 'Staff', 'role_status' => 1]);

        // Create departments
        $this->department1 = Department::create(['name' => 'IT', 'description' => 'Technology']);
        $this->department2 = Department::create(['name' => 'HR', 'description' => 'Human Resources']);

        // Create leave types
        $this->leaveType1 = LeaveType::create(['leave_type_name' => 'Annual', 'leave_duration' => 20]);
        $this->leaveType2 = LeaveType::create(['leave_type_name' => 'Sick', 'leave_duration' => 10]);

        // Create staff
        $this->staff1 = Staff::factory()->create([
            'department_id' => $this->department1->id,
            'role_id' => 2,
        ]);
        $this->staff2 = Staff::factory()->create([
            'department_id' => $this->department2->id,
            'role_id' => 2,
        ]);

        // Clear cache before each test
        Cache::flush();
    }

    protected function createApprovedLeave(Staff $staff, LeaveType $leaveType, string $startDate, int $days): StaffLeave
    {
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $startDate,
            'end_date' => Carbon::parse($startDate)->addDays($days - 1)->format('Y-m-d'),
            'leave_days' => $days,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
            'action_reason' => 'Approved',
        ]);

        return $leave;
    }

    public function test_leaves_by_department_returns_correct_aggregation(): void
    {
        // Create leaves for department 1
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-06-01', 3);

        // Create leaves for department 2
        $this->createApprovedLeave($this->staff2, $this->leaveType1, '2025-04-01', 2);

        $result = $this->analyticsService->getLeavesByDepartment(2025);

        $this->assertCount(2, $result);

        $itDept = $result->firstWhere('department_name', 'IT');
        $this->assertNotNull($itDept);
        $this->assertEquals(2, $itDept->total_requests);
        $this->assertEquals(8, $itDept->total_days);
        $this->assertEquals(1, $itDept->unique_staff);

        $hrDept = $result->firstWhere('department_name', 'HR');
        $this->assertNotNull($hrDept);
        $this->assertEquals(1, $hrDept->total_requests);
        $this->assertEquals(2, $hrDept->total_days);
    }

    public function test_leaves_by_department_filters_by_month(): void
    {
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-06-01', 3);

        $result = $this->analyticsService->getLeavesByDepartment(2025, 3);

        $this->assertCount(1, $result);
        $this->assertEquals(5, $result->first()->total_days);
    }

    public function test_leaves_by_type_returns_correct_aggregation(): void
    {
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);
        $this->createApprovedLeave($this->staff1, $this->leaveType2, '2025-04-01', 2);
        $this->createApprovedLeave($this->staff2, $this->leaveType1, '2025-05-01', 3);

        $result = $this->analyticsService->getLeavesByType(2025);

        $this->assertCount(2, $result);

        $annual = $result->firstWhere('leave_type_name', 'Annual');
        $this->assertEquals(2, $annual->total_requests);
        $this->assertEquals(8, $annual->total_days);
        $this->assertEquals(20, $annual->allocated_days);

        $sick = $result->firstWhere('leave_type_name', 'Sick');
        $this->assertEquals(1, $sick->total_requests);
        $this->assertEquals(2, $sick->total_days);
    }

    public function test_monthly_trends_returns_all_months(): void
    {
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-06-15', 3);

        $result = $this->analyticsService->getMonthlyTrends(2025);

        // Should have all 12 months
        $this->assertCount(12, $result);

        // March should have data
        $this->assertEquals(1, $result->get(3)->total_requests);
        $this->assertEquals(5, $result->get(3)->total_days);
        $this->assertEquals('March', $result->get(3)->month_name);

        // June should have data
        $this->assertEquals(1, $result->get(6)->total_requests);
        $this->assertEquals(3, $result->get(6)->total_days);

        // January should have zero
        $this->assertEquals(0, $result->get(1)->total_requests);
        $this->assertEquals(0, $result->get(1)->total_days);
    }

    public function test_yearly_trends_compares_multiple_years(): void
    {
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2023-03-01', 5);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2024-04-01', 8);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-05-01', 3);

        $result = $this->analyticsService->getYearlyTrends(2023, 2025);

        $this->assertCount(3, $result);

        $this->assertEquals(5, $result->get(2023)->total_days);
        $this->assertEquals(8, $result->get(2024)->total_days);
        $this->assertEquals(3, $result->get(2025)->total_days);
    }

    public function test_overview_stats_calculates_correctly(): void
    {
        // Create approved leaves
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-04-01', 3);

        // Create pending leave (no action)
        StaffLeave::create([
            'staff_id' => $this->staff2->id,
            'leave_type_id' => $this->leaveType1->id,
            'start_date' => '2025-05-01',
            'end_date' => '2025-05-02',
            'leave_days' => 2,
        ]);

        // Create rejected leave
        $rejectedLeave = StaffLeave::create([
            'staff_id' => $this->staff2->id,
            'leave_type_id' => $this->leaveType1->id,
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-02',
            'leave_days' => 2,
        ]);
        LeaveAction::create([
            'leave_id' => $rejectedLeave->id,
            'actionby' => $this->staff2->id,
            'status_id' => LeaveStatusEnum::REJECTED->value,
            'action_reason' => 'Rejected',
        ]);

        $stats = $this->analyticsService->getOverviewStats(2025);

        $this->assertEquals(4, $stats['total_requests']);
        $this->assertEquals(2, $stats['approved']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(1, $stats['rejected']);
        $this->assertEquals(8, $stats['total_days']);
        $this->assertEquals(4.0, $stats['avg_days_per_request']); // 8 days / 2 approved
        $this->assertEquals(50.0, $stats['approval_rate']); // 2 approved / 4 total
    }

    public function test_top_leave_takers_returns_correct_order(): void
    {
        // Staff 1 takes more leave
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 10);
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-04-01', 5);

        // Staff 2 takes less leave
        $this->createApprovedLeave($this->staff2, $this->leaveType1, '2025-05-01', 3);

        $result = $this->analyticsService->getTopLeaveTakers(2025, null, 10);

        $this->assertCount(2, $result);

        // First should be staff1 with most days
        $this->assertEquals($this->staff1->id, $result->first()->staff_id);
        $this->assertEquals(15, $result->first()->total_days);
        $this->assertEquals(2, $result->first()->total_requests);

        // Second should be staff2
        $this->assertEquals($this->staff2->id, $result->get(1)->staff_id);
        $this->assertEquals(3, $result->get(1)->total_days);
    }

    public function test_queries_cache_results(): void
    {
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);

        // First call - should hit database
        $result1 = $this->analyticsService->getLeavesByDepartment(2025);

        // Second call - should hit cache
        $result2 = $this->analyticsService->getLeavesByDepartment(2025);

        $this->assertEquals($result1->toArray(), $result2->toArray());

        // Verify cache key exists
        $this->assertTrue(Cache::has('analytics:leaves_by_dept:2025:all'));
    }

    public function test_export_data_returns_complete_structure(): void
    {
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);

        $data = $this->analyticsService->getExportData(2025);

        $this->assertArrayHasKey('year', $data);
        $this->assertArrayHasKey('month', $data);
        $this->assertArrayHasKey('generated_at', $data);
        $this->assertArrayHasKey('overview', $data);
        $this->assertArrayHasKey('by_department', $data);
        $this->assertArrayHasKey('by_type', $data);
        $this->assertArrayHasKey('monthly_trends', $data);
        $this->assertArrayHasKey('top_leave_takers', $data);

        $this->assertEquals(2025, $data['year']);
        $this->assertNull($data['month']);
    }

    public function test_only_approved_leaves_are_counted(): void
    {
        // Approved leave
        $this->createApprovedLeave($this->staff1, $this->leaveType1, '2025-03-01', 5);

        // Pending leave (no action)
        StaffLeave::create([
            'staff_id' => $this->staff1->id,
            'leave_type_id' => $this->leaveType1->id,
            'start_date' => '2025-04-01',
            'end_date' => '2025-04-05',
            'leave_days' => 5,
        ]);

        $result = $this->analyticsService->getLeavesByDepartment(2025);

        // Only approved leave should be counted
        $this->assertEquals(1, $result->first()->total_requests);
        $this->assertEquals(5, $result->first()->total_days);
    }
}
