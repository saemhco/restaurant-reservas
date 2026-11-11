<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Table> */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'number' => fake()->numberBetween(1, 50),
            'capacity' => fake()->randomElement([2, 4, 6, 8]),
            'location' => fake()->randomElement(['indoor', 'outdoor']),
        ];
    }
}
