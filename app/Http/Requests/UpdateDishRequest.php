<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('dishes.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['sometimes', 'exists:restaurants,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', Rule::in(['starter', 'main_course', 'dessert'])],
        ];
    }
}
