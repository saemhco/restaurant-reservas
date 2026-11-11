<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'table_id',
        'date',
        'time',
        'party_size',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'party_size' => 'integer',
        ];
    }

    public function setTimeAttribute(string $value): void
    {
        $this->attributes['time'] = strlen($value) === 5 ? "{$value}:00" : $value;
    }

    public function getTimeAttribute(?string $value): ?string
    {
        return $value === null ? null : substr($value, 0, 5);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function getDateTimeAttribute(): string
    {
        return sprintf('%s %s', $this->date?->format('Y-m-d'), $this->time);
    }
}
