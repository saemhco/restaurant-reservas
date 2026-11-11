<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableAvailableTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_available_tables_for_date_and_time(): void
    {
        $restaurant = Restaurant::factory()->create();
        $free = Table::factory()->create([
            'restaurant_id' => $restaurant->id,
            'number' => 1,
            'capacity' => 4,
        ]);
        $busy = Table::factory()->create([
            'restaurant_id' => $restaurant->id,
            'number' => 2,
            'capacity' => 4,
        ]);

        $date = now()->addDay()->format('Y-m-d');
        $time = '20:00';

        Reservation::factory()->create([
            'customer_id' => Customer::factory()->create()->id,
            'table_id' => $busy->id,
            'date' => $date,
            'time' => '20:00:00',
            'status' => 'confirmed',
            'party_size' => 2,
        ]);

        $response = $this->getJson("/api/tables/available?date={$date}&time={$time}&restaurant_id={$restaurant->id}");

        $response->assertOk()->assertJsonPath('success', true);

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($free->id, $ids);
        $this->assertNotContains($busy->id, $ids);
    }
}
