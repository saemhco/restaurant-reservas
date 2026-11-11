<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('dishes.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['starter', 'main_course', 'dessert'])],
        ];
    }
}
