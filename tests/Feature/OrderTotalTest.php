<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTotalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_returns_reservation_order_total(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $restaurant = Restaurant::factory()->create();
        $table = Table::factory()->create(['restaurant_id' => $restaurant->id, 'capacity' => 4]);
        $customer = Customer::factory()->create();
        $reservation = Reservation::factory()->confirmed()->create([
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'party_size' => 2,
        ]);

        $dishA = Dish::factory()->create(['restaurant_id' => $restaurant->id, 'price' => 10.00]);
        $dishB = Dish::factory()->create(['restaurant_id' => $restaurant->id, 'price' => 5.50]);

        $order = Order::factory()->create(['reservation_id' => $reservation->id]);
        $order->dishes()->sync([
            $dishA->id => ['quantity' => 2, 'unit_price' => 10.00],
            $dishB->id => ['quantity' => 1, 'unit_price' => 5.50],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/reservations/{$reservation->id}/total");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 25.5);
    }
}
