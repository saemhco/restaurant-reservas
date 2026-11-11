<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'restaurants.manage' => 'Manage restaurants',
            'tables.manage' => 'Manage tables',
            'dishes.manage' => 'Manage dishes',
            'employees.manage' => 'Manage employees',
            'customers.manage' => 'Manage customers',
            'reservations.manage' => 'Manage all reservations',
            'reservations.own' => 'Manage own reservations',
            'orders.manage' => 'Manage orders',
        ];

        foreach ($permissions as $name => $label) {
            Permission::query()->firstOrCreate(
                ['name' => $name],
                ['label' => $label]
            );
        }

        $roles = [
            'admin' => [
                'label' => 'Administrator',
                'permissions' => array_keys($permissions),
            ],
            'manager' => [
                'label' => 'Restaurant Manager',
                'permissions' => [
                    'tables.manage',
                    'dishes.manage',
                    'employees.manage',
                    'customers.manage',
                    'reservations.manage',
                    'orders.manage',
                ],
            ],
            'staff' => [
                'label' => 'Staff',
                'permissions' => [
                    'reservations.manage',
                    'orders.manage',
                    'customers.manage',
                ],
            ],
            'customer' => [
                'label' => 'Customer',
                'permissions' => [
                    'reservations.own',
                ],
            ],
        ];

        foreach ($roles as $name => $config) {
            $role = Role::query()->firstOrCreate(
                ['name' => $name],
                ['label' => $config['label']]
            );

            $permissionIds = Permission::query()
                ->whereIn('name', $config['permissions'])
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
