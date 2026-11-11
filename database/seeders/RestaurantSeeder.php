<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::factory()->create([
            'name' => 'La Mesa Verde',
            'address' => '123 Main Street, Downtown',
            'phone' => '+1-555-0100',
            'opening_time' => '12:00',
            'closing_time' => '23:00',
        ]);

        Restaurant::factory()->create([
            'name' => 'Ocean Breeze Grill',
            'address' => '45 Harbor Avenue, Seaside',
            'phone' => '+1-555-0200',
            'opening_time' => '11:00',
            'closing_time' => '22:00',
        ]);
    }
}
