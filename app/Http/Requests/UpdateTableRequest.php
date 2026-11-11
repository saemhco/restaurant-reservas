<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('tables.manage') ?? false;
    }

    public function rules(): array
    {
        $table = $this->route('table');
        $restaurantId = $this->input('restaurant_id', $table?->restaurant_id);

        return [
            'restaurant_id' => ['sometimes', 'exists:restaurants,id'],
            'number' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::unique('tables')
                    ->where(fn ($q) => $q->where('restaurant_id', $restaurantId))
                    ->ignore($table?->id),
            ],
            'capacity' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'location' => ['sometimes', Rule::in(['indoor', 'outdoor'])],
        ];
    }
}
