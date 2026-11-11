<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('orders.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => ['required', 'exists:reservations,id', 'unique:orders,reservation_id'],
            'ordered_at' => ['sometimes', 'date'],
            'dishes' => ['required', 'array', 'min:1'],
            'dishes.*.dish_id' => ['required', 'exists:dishes,id'],
            'dishes.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
