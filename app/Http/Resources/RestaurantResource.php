<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'schedule' => $this->schedule,
            'tables_count' => $this->whenCounted('tables'),
            'dishes_count' => $this->whenCounted('dishes'),
            'employees_count' => $this->whenCounted('employees'),
            'tables' => TableResource::collection($this->whenLoaded('tables')),
            'dishes' => DishResource::collection($this->whenLoaded('dishes')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
