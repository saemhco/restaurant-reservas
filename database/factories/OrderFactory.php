<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory()->confirmed(),
            'ordered_at' => now(),
        ];
    }
}
