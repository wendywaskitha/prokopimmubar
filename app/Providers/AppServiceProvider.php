<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Agenda;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set created_by automatically when creating agenda
        Agenda::creating(function ($agenda) {
            if (auth()->check()) {
                $agenda->created_by = auth()->id();
            }
        });
    }
}
