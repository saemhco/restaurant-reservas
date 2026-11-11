<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::query()->each(function (Restaurant $restaurant) {
            Employee::factory()->create([
                'restaurant_id' => $restaurant->id,
                'name' => 'Alex Manager',
                'position' => 'manager',
            ]);

            Employee::factory()->count(2)->create([
                'restaurant_id' => $restaurant->id,
                'position' => 'waiter',
            ]);

            Employee::factory()->create([
                'restaurant_id' => $restaurant->id,
                'position' => 'chef',
            ]);
        });
    }
}
