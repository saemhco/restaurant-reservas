<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    /** @use HasFactory<\Database\Factories\TableFactory> */
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'number',
        'capacity',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'capacity' => 'integer',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** Mesas sin reserva activa (pending/confirmed) en fecha y hora dadas. */
    public function scopeAvailable(Builder $query, string $date, string $time): Builder
    {
        $normalizedTime = strlen($time) === 5 ? "{$time}:00" : $time;

        return $query->whereDoesntHave('reservations', function (Builder $q) use ($date, $normalizedTime) {
            $q->whereDate('date', $date)
                ->where('time', $normalizedTime)
                ->whereIn('status', ['pending', 'confirmed']);
        });
    }

    public function scopeIndoor(Builder $query): Builder
    {
        return $query->where('location', 'indoor');
    }

    public function scopeOutdoor(Builder $query): Builder
    {
        return $query->where('location', 'outdoor');
    }

    public function getLabelAttribute(): string
    {
        return sprintf('Table %d (%s, %d guests)', $this->number, $this->location, $this->capacity);
    }
}
