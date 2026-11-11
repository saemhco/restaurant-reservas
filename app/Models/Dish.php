<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dish extends Model
{
    /** @use HasFactory<\Database\Factories\DishFactory> */
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'price',
        'description',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_dish')
            ->withPivot(['quantity', 'unit_price'])
            ->withTimestamps();
    }

    public function scopeStarters(Builder $query): Builder
    {
        return $query->where('type', 'starter');
    }

    public function scopeMainCourses(Builder $query): Builder
    {
        return $query->where('type', 'main_course');
    }

    public function scopeDesserts(Builder $query): Builder
    {
        return $query->where('type', 'dessert');
    }

    public function scopeMostOrdered(Builder $query, int $limit = 10): Builder
    {
        return $query
            ->select('dishes.*')
            ->selectRaw('COALESCE(SUM(order_dish.quantity), 0) as total_sold')
            ->leftJoin('order_dish', 'dishes.id', '=', 'order_dish.dish_id')
            ->groupBy('dishes.id')
            ->orderByDesc('total_sold')
            ->limit($limit);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$'.number_format((float) $this->price, 2, '.', ',');
    }
}
