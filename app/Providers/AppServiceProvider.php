<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\View\Composers\NavigationComposer;
use Illuminate\Support\ServiceProvider;
use App\Models\Demande;
use App\Observers\DemandeObserver;
use App\Contracts\TranscripteurAutomatique;
use App\Services\Transcription\TranscripteurOvhWhisper;
use App\Services\Transcription\TranscripteurSimule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    
    public function register(): void
    {
        $this->app->bind(TranscripteurAutomatique::class, function () {
            return config('ovhcloud.token')
                ? new TranscripteurOvhWhisper()
                : new TranscripteurSimule();
        });
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