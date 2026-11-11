<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'number' => $this->number,
            'capacity' => $this->capacity,
            'location' => $this->location,
            'label' => $this->label,
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
