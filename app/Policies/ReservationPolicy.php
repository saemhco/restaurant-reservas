<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('reservations.manage')
            || $user->hasPermission('reservations.own');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->hasPermission('reservations.manage')) {
            return true;
        }

        return $user->hasPermission('reservations.own')
            && $user->customer
            && $reservation->customer_id === $user->customer->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('reservations.manage')
            || $user->hasPermission('reservations.own');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        if ($user->hasPermission('reservations.manage')) {
            return true;
        }

        return $user->hasPermission('reservations.own')
            && $user->customer
            && $reservation->customer_id === $user->customer->id;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $this->update($user, $reservation);
    }
}
