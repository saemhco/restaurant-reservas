<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Dish> */
class DishFactory extends Factory
{
    protected $model = Dish::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 5, 80),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['starter', 'main_course', 'dessert']),
        ];
    }
}
