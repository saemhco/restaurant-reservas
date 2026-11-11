<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'table_id' => $this->table_id,
            'date' => $this->date?->format('Y-m-d'),
            'time' => $this->time,
            'date_time' => $this->date_time,
            'party_size' => $this->party_size,
            'status' => $this->status,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'table' => new TableResource($this->whenLoaded('table')),
            'order' => new OrderResource($this->whenLoaded('order')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
