<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\View\Composers\NavigationComposer;
use Illuminate\Support\ServiceProvider;
use App\Models\Demande;
use App\Observers\DemandeObserver;

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
        View::composer('layouts.app', NavigationComposer::class);
        Demande::observe(DemandeObserver::class);
    }
}