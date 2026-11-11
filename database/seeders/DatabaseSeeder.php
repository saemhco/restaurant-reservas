<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            RestaurantSeeder::class,
            CustomerSeeder::class,
            TableSeeder::class,
            DishSeeder::class,
            EmployeeSeeder::class,
            DemoUserSeeder::class,
            ReservationSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
