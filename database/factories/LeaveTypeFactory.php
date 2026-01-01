<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = LeaveType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'leave_type_name' => fake()->unique()->randomElement([
                'Annual Leave',
                'Sick Leave',
                'Maternity Leave',
                'Paternity Leave',
                'Study Leave',
                'Compassionate Leave',
            ]),
            'leave_duration' => fake()->numberBetween(5, 30),
        ];
    }

    /**
     * State for annual leave.
     */
    public function annual(): static
    {
        return $this->state(fn(array $attributes) => [
            'leave_type_name' => 'Annual Leave',
            'leave_duration' => 21,
        ]);
    }

    /**
     * State for sick leave.
     */
    public function sick(): static
    {
        return $this->state(fn(array $attributes) => [
            'leave_type_name' => 'Sick Leave',
            'leave_duration' => 14,
        ]);
    }

    /**
     * State for maternity leave.
     */
    public function maternity(): static
    {
        return $this->state(fn(array $attributes) => [
            'leave_type_name' => 'Maternity Leave',
            'leave_duration' => 90,
        ]);
    }
}
