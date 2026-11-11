<?php

namespace App\Http\Requests;

use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');

        return $this->user()?->can('update', $reservation) ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'table_id' => ['sometimes', 'exists:tables,id'],
            'date' => ['sometimes', 'date', 'after_or_equal:today'],
            'time' => ['sometimes', 'date_format:H:i'],
            'party_size' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ];

        if ($this->user()?->hasPermission('reservations.manage')) {
            $rules['customer_id'] = ['sometimes', 'exists:customers,id'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $reservation = $this->route('reservation');
            $tableId = $this->input('table_id', $reservation->table_id);
            $date = $this->input('date', $reservation->date?->format('Y-m-d'));
            $time = $this->input('time', $reservation->time);
            $partySize = $this->input('party_size', $reservation->party_size);

            $table = Table::find($tableId);

            if ($table && $partySize > $table->capacity) {
                $validator->errors()->add(
                    'party_size',
                    "This table only seats {$table->capacity} guests."
                );
            }

            if ($this->filled('table_id') || $this->filled('date') || $this->filled('time')) {
                $unavailable = Table::query()
                    ->whereKey($tableId)
                    ->whereDoesntHave('reservations', function ($q) use ($date, $time, $reservation) {
                        $normalizedTime = strlen($time) === 5 ? "{$time}:00" : $time;
                        $q->whereDate('date', $date)
                            ->where('time', $normalizedTime)
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->where('id', '!=', $reservation->id);
                    })
                    ->doesntExist();

                // available() no excluye la propia reserva; comprobación manual arriba
                $conflict = Table::query()
                    ->whereKey($tableId)
                    ->whereHas('reservations', function ($q) use ($date, $time, $reservation) {
                        $normalizedTime = strlen($time) === 5 ? "{$time}:00" : $time;
                        $q->whereDate('date', $date)
                            ->where('time', $normalizedTime)
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->where('id', '!=', $reservation->id);
                    })
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add(
                        'table_id',
                        'This table is not available at that date and time.'
                    );
                }

                unset($unavailable);
            }
        });
    }
}
