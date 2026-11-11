<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    /** @use HasFactory<\Database\Factories\RestaurantFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'opening_time',
        'closing_time',
    ];

    public function setOpeningTimeAttribute(string $value): void
    {
        $this->attributes['opening_time'] = strlen($value) === 5 ? "{$value}:00" : $value;
    }

    public function setClosingTimeAttribute(string $value): void
    {
        $this->attributes['closing_time'] = strlen($value) === 5 ? "{$value}:00" : $value;
    }

    public function getOpeningTimeAttribute(?string $value): ?string
    {
        return $value ? substr($value, 0, 5) : null;
    }

    public function getClosingTimeAttribute(?string $value): ?string
    {
        return $value ? substr($value, 0, 5) : null;
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function getScheduleAttribute(): string
    {
        return sprintf('%s - %s', $this->opening_time, $this->closing_time);
    }
}
