<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'position' => $this->position,
            'formatted_position' => $this->formatted_position,
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
