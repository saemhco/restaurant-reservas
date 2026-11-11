<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'formatted_price' => $this->formatted_price,
            'description' => $this->description,
            'type' => $this->type,
            'total_sold' => $this->when(isset($this->total_sold), (int) $this->total_sold),
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
