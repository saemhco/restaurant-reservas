<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $restaurant = Restaurant::query()->first();

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@demo.test'],
            ['name' => 'Demo Admin', 'password' => $password]
        );
        $admin->assignRole('admin');

        $manager = User::query()->firstOrCreate(
            ['email' => 'manager@demo.test'],
            ['name' => 'Demo Manager', 'password' => $password]
        );
        $manager->assignRole('manager');

        if ($restaurant) {
            Employee::query()->updateOrCreate(
                ['user_id' => $manager->id],
                [
                    'restaurant_id' => $restaurant->id,
                    'name' => $manager->name,
                    'position' => 'manager',
                ]
            );
        }

        $staff = User::query()->firstOrCreate(
            ['email' => 'staff@demo.test'],
            ['name' => 'Demo Staff', 'password' => $password]
        );
        $staff->assignRole('staff');

        if ($restaurant) {
            Employee::query()->updateOrCreate(
                ['user_id' => $staff->id],
                [
                    'restaurant_id' => $restaurant->id,
                    'name' => $staff->name,
                    'position' => 'waiter',
                ]
            );
        }

        $customerUser = User::query()->firstOrCreate(
            ['email' => 'customer@demo.test'],
            ['name' => 'Demo Customer', 'password' => $password]
        );
        $customerUser->assignRole('customer');

        Customer::query()->updateOrCreate(
            ['email' => 'customer@demo.test'],
            [
                'user_id' => $customerUser->id,
                'name' => $customerUser->name,
                'phone' => '+1-555-0999',
            ]
        );

        // Asegurar que los roles existen (por si se llama fuera de orden)
        Role::query()->whereIn('name', ['admin', 'manager', 'staff', 'customer'])->exists();
    }
}
