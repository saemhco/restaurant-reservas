<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::query()->each(function (Restaurant $restaurant) {
            foreach (range(1, 8) as $number) {
                Table::factory()->create([
                    'restaurant_id' => $restaurant->id,
                    'number' => $number,
                    'capacity' => $number <= 4 ? 2 : ($number <= 6 ? 4 : 6),
                    'location' => $number % 2 === 0 ? 'outdoor' : 'indoor',
                ]);
            }
        });
    }
}
