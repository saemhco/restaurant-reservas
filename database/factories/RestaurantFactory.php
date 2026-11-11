<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Restaurant> */
class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Restaurant',
            'address' => fake()->streetAddress().', '.fake()->city(),
            'phone' => fake()->numerify('+1-###-###-####'),
            'opening_time' => '12:00:00',
            'closing_time' => '23:00:00',
        ];
    }
}
