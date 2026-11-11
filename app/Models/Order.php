<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'order_dish')
            ->withPivot(['quantity', 'unit_price'])
            ->withTimestamps();
    }

    public function calculateTotal(): float
    {
        return (float) $this->dishes->sum(function (Dish $dish) {
            return $dish->pivot->quantity * $dish->pivot->unit_price;
        });
    }

    public function getTotalAttribute(): float
    {
        if ($this->relationLoaded('dishes')) {
            return $this->calculateTotal();
        }

        return (float) $this->dishes()
            ->selectRaw('SUM(order_dish.quantity * order_dish.unit_price) as aggregate')
            ->value('aggregate');
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$'.number_format($this->total, 2, '.', ',');
    }
}
