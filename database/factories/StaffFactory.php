<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\Role;
use App\Models\LeaveLevel;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $gender = fake()->boolean();

        return [
            'staff_number' => fake()->unique()->numerify('STF#####'),
            'title' => fake()->title($gender ? 'male' : 'female'),
            'firstname' => fake()->firstName($gender ? 'male' : 'female'),
            'lastname' => fake()->lastName(),
            'othername' => fake()->optional(0.3)->firstName(),
            'dob' => fake()->dateTimeBetween('-60 years', '-20 years'),
            'mobile_number' => fake()->unique()->numerify('+254#########'),
            'gender' => $gender,
            'is_active' => true,
            'date_joined' => fake()->dateTimeBetween('-5 years', 'now'),
            'leave_level_id' => null,
            'total_leave_days' => fake()->numberBetween(15, 30),
            'role_id' => RoleEnum::NORMAL->value,
            'supervisor_id' => null,
        ];
    }

    /**
     * State for admin role.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleEnum::ADMIN->value,
        ]);
    }

    /**
     * State for HR role.
     */
    public function hr(): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleEnum::HR->value,
        ]);
    }

    /**
     * State for Director General role.
     */
    public function dg(): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleEnum::DG->value,
        ]);
    }

    /**
     * State for Director role.
     */
    public function director(): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleEnum::DIRECTOR->value,
        ]);
    }

    /**
     * State for inactive staff.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create with associated user account.
     */
    public function withUser(): static
    {
        return $this->afterCreating(function (Staff $staff) {
            \App\Models\User::factory()->create([
                'id' => $staff->id,
                'email' => strtolower($staff->firstname . '.' . $staff->lastname . '@example.com'),
            ]);
        });
    }
}
