<?php

namespace Database\Factories;

use App\Models\StaffLeave;
use App\Models\Staff;
use App\Models\LeaveType;
use App\Models\LeaveAction;
use App\Enums\LeaveStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffLeaveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = StaffLeave::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = Carbon::parse($startDate)->addDays(fake()->numberBetween(1, 5));
        $leaveDays = $startDate->diff($endDate)->days + 1;

        return [
            'staff_id' => Staff::factory(),
            'leave_type_id' => 1, // Annual Leave
            'start_date' => $startDate,
            'end_date' => $endDate,
            'leave_days' => $leaveDays,
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (StaffLeave $leave) {
            // Create initial unattended action
            LeaveAction::create([
                'leave_id' => $leave->id,
                'actionby' => $leave->staff_id,
                'status_id' => LeaveStatusEnum::UNATTENDED->value,
            ]);
        });
    }

    /**
     * State for approved leave.
     */
    public function approved(): static
    {
        return $this->afterCreating(function (StaffLeave $leave) {
            LeaveAction::create([
                'leave_id' => $leave->id,
                'actionby' => $leave->staff_id,
                'status_id' => LeaveStatusEnum::APPROVED->value,
            ]);
        });
    }

    /**
     * State for rejected leave.
     */
    public function rejected(): static
    {
        return $this->afterCreating(function (StaffLeave $leave) {
            LeaveAction::create([
                'leave_id' => $leave->id,
                'actionby' => $leave->staff_id,
                'status_id' => LeaveStatusEnum::REJECTED->value,
                'action_reason' => 'Rejected for testing purposes',
            ]);
        });
    }

    /**
     * State for recommended leave.
     */
    public function recommended(): static
    {
        return $this->afterCreating(function (StaffLeave $leave) {
            LeaveAction::create([
                'leave_id' => $leave->id,
                'actionby' => $leave->staff_id,
                'status_id' => LeaveStatusEnum::RECOMMENDED->value,
            ]);
        });
    }

    /**
     * State for cancelled leave.
     */
    public function cancelled(): static
    {
        return $this->afterCreating(function (StaffLeave $leave) {
            LeaveAction::create([
                'leave_id' => $leave->id,
                'actionby' => $leave->staff_id,
                'status_id' => LeaveStatusEnum::CANCELLED->value,
            ]);
        });
    }

    /**
     * Set specific dates for the leave.
     */
    public function forDates(string $start, string $end): static
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        $leaveDays = $startDate->diffInDays($endDate) + 1;

        return $this->state(fn(array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'leave_days' => $leaveDays,
        ]);
    }

    /**
     * Set leave to start today.
     */
    public function startingToday(): static
    {
        return $this->state(fn(array $attributes) => [
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(2),
            'leave_days' => 3,
        ]);
    }

    /**
     * Set leave to be in the past.
     */
    public function inPast(): static
    {
        $start = Carbon::now()->subDays(10);
        $end = Carbon::now()->subDays(5);

        return $this->state(fn(array $attributes) => [
            'start_date' => $start,
            'end_date' => $end,
            'leave_days' => 6,
        ]);
    }
}
