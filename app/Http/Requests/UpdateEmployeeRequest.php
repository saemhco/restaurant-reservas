<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('employees.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['sometimes', 'exists:restaurants,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'position' => ['sometimes', Rule::in(['waiter', 'chef', 'manager'])],
        ];
    }
}
