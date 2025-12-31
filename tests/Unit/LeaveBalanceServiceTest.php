<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\LeaveBalanceService;
use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Models\LeaveAction;
use App\Enums\LeaveStatusEnum;
use Carbon\Carbon;

class LeaveBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeaveBalanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LeaveBalanceService();
        $this->seed(\Database\Seeders\RolesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveTypesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveStatusesTableSeeder::class);
    }

    /**
     * Test getting balance for staff with no approved leaves.
     */
    public function test_get_balance_for_staff_with_no_leaves(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

        $balance = $this->service->getBalance($staff->id);

        $this->assertEquals(21, $balance);
    }

    /**
     * Test balance is reduced by approved leaves.
     */
    public function test_balance_reduced_by_approved_leaves(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

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

        $balance = $this->service->getBalance($staff->id);

        $this->assertEquals(16, $balance);
    }

    /**
     * Test pending leaves DO reduce balance (strict compliance rule).
     * 
     * Business Rule: Pending (Unattended) requests must temporarily deduct
     * from available balance to prevent over-booking leave days.
     */
    public function test_pending_leaves_reduce_balance(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

        // Create a pending leave of 5 days
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
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
        ]);

        $balance = $this->service->getBalance($staff->id);

        // Pending leaves temporarily deduct from balance (strict compliance)
        $this->assertEquals(16, $balance);
    }

    /**
     * Test can apply check with sufficient balance.
     */
    public function test_can_apply_with_sufficient_balance(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

        $canApply = $this->service->canApply($staff->id, 10);

        $this->assertTrue($canApply);
    }

    /**
     * Test can apply check with insufficient balance.
     */
    public function test_cannot_apply_with_insufficient_balance(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 5,
        ]);

        $canApply = $this->service->canApply($staff->id, 10);

        $this->assertFalse($canApply);
    }

    /**
     * Test overlapping leave detection.
     */
    public function test_detects_overlapping_leaves(): void
    {
        $staff = Staff::factory()->create();

        // Create an approved leave from Jan 10-15
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => '2025-01-10',
            'end_date' => '2025-01-15',
            'leave_days' => 6,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
        ]);

        // Test overlapping date range (Jan 12-17)
        $hasOverlap = $this->service->hasOverlappingLeave(
            $staff->id,
            '2025-01-12',
            '2025-01-17'
        );

        $this->assertTrue($hasOverlap);
    }

    /**
     * Test non-overlapping leave detection.
     */
    public function test_no_overlap_for_different_dates(): void
    {
        $staff = Staff::factory()->create();

        // Create an approved leave from Jan 10-15
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => 1,
            'start_date' => '2025-01-10',
            'end_date' => '2025-01-15',
            'leave_days' => 6,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
        ]);

        // Test non-overlapping date range (Jan 20-25)
        $hasOverlap = $this->service->hasOverlappingLeave(
            $staff->id,
            '2025-01-20',
            '2025-01-25'
        );

        $this->assertFalse($hasOverlap);
    }

    /**
     * Test balance breakdown returns correct structure.
     */
    public function test_balance_breakdown_structure(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

        $breakdown = $this->service->getBalanceBreakdown($staff->id);

        $this->assertArrayHasKey('total_allowance', $breakdown);
        $this->assertArrayHasKey('total_used', $breakdown);
        $this->assertArrayHasKey('remaining', $breakdown);
        $this->assertArrayHasKey('by_type', $breakdown);
        $this->assertEquals(21, $breakdown['total_allowance']);
    }

    /**
     * Test returns 0 for non-existent staff.
     */
    public function test_returns_zero_for_nonexistent_staff(): void
    {
        $balance = $this->service->getBalance(99999);

        $this->assertEquals(0, $balance);
    }

    /**
     * Test balance breakdown includes allocated per leave type.
     */
    public function test_balance_breakdown_includes_allocated_per_type(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

        $breakdown = $this->service->getBalanceBreakdown($staff->id);

        // Each leave type should have an 'allocated' field
        foreach ($breakdown['by_type'] as $typeName => $data) {
            $this->assertArrayHasKey('allocated', $data, "Type '{$typeName}' missing 'allocated' key");
            $this->assertIsInt($data['allocated']);
        }
    }

    /**
     * Test balance breakdown calculates remaining per leave type correctly.
     */
    public function test_balance_breakdown_calculates_remaining_per_type(): void
    {
        $staff = Staff::factory()->create([
            'total_leave_days' => 21,
        ]);

        // Get a leave type and create an approved leave
        $leaveType = LeaveType::first();
        $allocatedDays = $leaveType->leave_duration ?? 0;

        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
            'leave_days' => 3,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $staff->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
        ]);

        $breakdown = $this->service->getBalanceBreakdown($staff->id);

        // Find the leave type in breakdown
        $typeData = $breakdown['by_type'][$leaveType->leave_type_name] ?? null;
        $this->assertNotNull($typeData, "Leave type '{$leaveType->leave_type_name}' not found in breakdown");

        // Verify remaining is correctly calculated
        $expectedRemaining = max(0, $allocatedDays - 3);
        $this->assertEquals(3, $typeData['used']);
        $this->assertEquals($allocatedDays, $typeData['allocated']);
        $this->assertEquals($expectedRemaining, $typeData['remaining']);
    }
}
