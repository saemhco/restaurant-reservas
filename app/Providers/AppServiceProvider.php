<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Policies\ReservationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Reservation::class, ReservationPolicy::class);

        // Admin bypass para cualquier Gate/Policy
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
