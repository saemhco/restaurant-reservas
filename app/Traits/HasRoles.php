<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function assignRole(string|Role $role): void
    {
        $roleModel = $role instanceof Role
            ? $role
            : Role::query()->where('name', $role)->firstOrFail();

        $this->roles()->syncWithoutDetaching([$roleModel->id]);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->permissions()->contains('name', $permission);
    }

    /** @return Collection<int, Permission> */
    public function permissions(): Collection
    {
        $this->loadMissing('roles.permissions');

        return $this->roles
            ->flatMap(fn (Role $role) => $role->permissions)
            ->unique('id')
            ->values();
    }
}
