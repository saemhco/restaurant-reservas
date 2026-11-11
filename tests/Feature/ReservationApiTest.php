<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\Table;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function actingAsCustomer(): array
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $customer = Customer::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

        return [$user, $customer];
    }

    public function test_customer_can_create_a_reservation(): void
    {
        [$user, $customer] = $this->actingAsCustomer();

        $restaurant = Restaurant::factory()->create();
        $table = Table::factory()->create([
            'restaurant_id' => $restaurant->id,
            'number' => 1,
            'capacity' => 4,
        ]);

        $payload = [
            'table_id' => $table->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '20:00',
            'party_size' => 2,
            'status' => 'pending',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reservations', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.table_id', $table->id)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('reservations', [
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'party_size' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_does_not_allow_booking_an_occupied_table(): void
    {
        [$user, $customer] = $this->actingAsCustomer();

        $restaurant = Restaurant::factory()->create();
        $table = Table::factory()->create([
            'restaurant_id' => $restaurant->id,
            'capacity' => 4,
        ]);
        $fecha = now()->addDays(2)->format('Y-m-d');
        $hora = '19:00';

        $this->actingAs($user, 'sanctum')->postJson('/api/reservations', [
            'table_id' => $table->id,
            'date' => $fecha,
            'time' => $hora,
            'party_size' => 2,
        ])->assertCreated();

        $otherUser = User::factory()->create();
        $otherUser->assignRole('customer');
        Customer::factory()->create([
            'user_id' => $otherUser->id,
            'email' => $otherUser->email,
        ]);

        $this->actingAs($otherUser, 'sanctum')->postJson('/api/reservations', [
            'table_id' => $table->id,
            'date' => $fecha,
            'time' => $hora,
            'party_size' => 2,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['table_id'], 'errors');
    }

    public function test_guest_cannot_create_reservation(): void
    {
        $restaurant = Restaurant::factory()->create();
        $table = Table::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->postJson('/api/reservations', [
            'table_id' => $table->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '20:00',
            'party_size' => 2,
        ])->assertUnauthorized();
    }

    public function test_customer_only_sees_own_reservations(): void
    {
        [$user, $customer] = $this->actingAsCustomer();
        $restaurant = Restaurant::factory()->create();
        $table = Table::factory()->create(['restaurant_id' => $restaurant->id, 'capacity' => 4]);

        $this->actingAs($user, 'sanctum')->postJson('/api/reservations', [
            'table_id' => $table->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '18:00',
            'party_size' => 2,
        ])->assertCreated();

        $otherCustomer = Customer::factory()->create();
        \App\Models\Reservation::factory()->create([
            'customer_id' => $otherCustomer->id,
            'table_id' => $table->id,
            'date' => now()->addDays(3)->format('Y-m-d'),
            'time' => '19:00:00',
            'party_size' => 2,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/reservations');

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($customer->id, $response->json('data.0.customer_id'));
    }
}
