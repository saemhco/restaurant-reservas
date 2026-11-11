<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Employee> */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'user_id' => null,
            'name' => fake()->name(),
            'position' => fake()->randomElement(['waiter', 'chef', 'manager']),
        ];
    }
}
