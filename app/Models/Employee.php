<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'name',
        'position',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWaiters(Builder $query): Builder
    {
        return $query->where('position', 'waiter');
    }

    public function scopeChefs(Builder $query): Builder
    {
        return $query->where('position', 'chef');
    }

    public function scopeManagers(Builder $query): Builder
    {
        return $query->where('position', 'manager');
    }

    public function getFormattedPositionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->position));
    }
}
