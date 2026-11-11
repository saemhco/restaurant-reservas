<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class DishSeeder extends Seeder
{
    public function run(): void
    {
        $menu = [
            ['name' => 'Tomato Bruschetta', 'type' => 'starter', 'price' => 8.50],
            ['name' => 'Caesar Salad', 'type' => 'starter', 'price' => 9.00],
            ['name' => 'Grilled Salmon', 'type' => 'main_course', 'price' => 24.00],
            ['name' => 'Ribeye Steak', 'type' => 'main_course', 'price' => 32.00],
            ['name' => 'Mushroom Risotto', 'type' => 'main_course', 'price' => 18.50],
            ['name' => 'Chocolate Lava Cake', 'type' => 'dessert', 'price' => 9.50],
            ['name' => 'Tiramisu', 'type' => 'dessert', 'price' => 8.00],
        ];

        Restaurant::query()->each(function (Restaurant $restaurant) use ($menu) {
            foreach ($menu as $item) {
                Dish::factory()->create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'price' => $item['price'],
                    'description' => "House specialty: {$item['name']}",
                ]);
            }
        });
    }
}
