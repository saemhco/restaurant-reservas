<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_id' => $this->reservation_id,
            'ordered_at' => $this->ordered_at,
            'total' => $this->when(
                $this->relationLoaded('dishes'),
                fn () => round($this->calculateTotal(), 2)
            ),
            'formatted_total' => $this->when(
                $this->relationLoaded('dishes'),
                fn () => $this->formatted_total
            ),
            'dishes' => DishResource::collection($this->whenLoaded('dishes')),
            'reservation' => new ReservationResource($this->whenLoaded('reservation')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
