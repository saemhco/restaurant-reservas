<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->get();
        $tables = Table::query()->get();

        if ($customers->isEmpty() || $tables->isEmpty()) {
            return;
        }

        foreach (range(1, 10) as $i) {
            $table = $tables->random();

            Reservation::factory()->create([
                'customer_id' => $customers->random()->id,
                'table_id' => $table->id,
                'party_size' => min(fake()->numberBetween(1, 4), $table->capacity),
                'date' => now()->addDays($i)->format('Y-m-d'),
                'time' => fake()->randomElement(['13:00:00', '19:00:00', '20:00:00']),
                'status' => fake()->randomElement(['pending', 'confirmed']),
            ]);
        }
    }
}
