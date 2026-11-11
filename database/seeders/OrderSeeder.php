<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Reservation::query()
            ->where('status', 'confirmed')
            ->with('table')
            ->take(5)
            ->get()
            ->each(function (Reservation $reservation) {
                $order = Order::factory()->create([
                    'reservation_id' => $reservation->id,
                ]);

                $dishes = Dish::query()
                    ->where('restaurant_id', $reservation->table->restaurant_id)
                    ->inRandomOrder()
                    ->take(2)
                    ->get();

                $sync = [];
                foreach ($dishes as $dish) {
                    $sync[$dish->id] = [
                        'quantity' => fake()->numberBetween(1, 3),
                        'unit_price' => $dish->price,
                    ];
                }

                $order->dishes()->sync($sync);
            });
    }
}
