<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('orders.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'ordered_at' => ['sometimes', 'date'],
            'dishes' => ['sometimes', 'array', 'min:1'],
            'dishes.*.dish_id' => ['required_with:dishes', 'exists:dishes,id'],
            'dishes.*.quantity' => ['required_with:dishes', 'integer', 'min:1'],
        ];
    }
}
