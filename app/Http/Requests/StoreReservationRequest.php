<?php

namespace App\Http\Requests;

use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Reservation::class) ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'table_id' => ['required', 'exists:tables,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'party_size' => ['required', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ];

        // Staff/admin pueden asignar cualquier customer; customers usan el suyo
        if ($this->user()?->hasPermission('reservations.manage')) {
            $rules['customer_id'] = ['required', 'exists:customers,id'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $table = Table::find($this->table_id);

            if ($table && $this->party_size > $table->capacity) {
                $validator->errors()->add(
                    'party_size',
                    "This table only seats {$table->capacity} guests."
                );
            }

            $unavailable = Table::query()
                ->whereKey($this->table_id)
                ->available($this->date, $this->time)
                ->doesntExist();

            if ($unavailable) {
                $validator->errors()->add(
                    'table_id',
                    'This table is not available at that date and time.'
                );
            }
        });
    }
}
