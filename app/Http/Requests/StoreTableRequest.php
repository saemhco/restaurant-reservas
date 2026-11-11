<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('tables.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('tables')->where(fn ($q) => $q->where('restaurant_id', $this->restaurant_id)),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'location' => ['required', Rule::in(['indoor', 'outdoor'])],
        ];
    }
}
