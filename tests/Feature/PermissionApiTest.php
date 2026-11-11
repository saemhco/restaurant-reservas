<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_customer_cannot_create_restaurant(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/restaurants', [
                'name' => 'Forbidden Place',
                'address' => '1 Test St',
                'opening_time' => '12:00',
                'closing_time' => '22:00',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_create_restaurant(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/restaurants', [
                'name' => 'Admin Place',
                'address' => '1 Admin St',
                'opening_time' => '12:00',
                'closing_time' => '22:00',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Admin Place');

        $this->assertDatabaseHas('restaurants', ['name' => 'Admin Place']);
    }

    public function test_customer_cannot_manage_orders(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        Restaurant::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders')
            ->assertForbidden();
    }
}
