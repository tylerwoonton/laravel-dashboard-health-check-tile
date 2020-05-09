<?php

namespace TylerWoonton\HealthCheckTile;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use TylerWoonton\HealthCheckTile\Commands\FetchHealthCheckDataCommand;

class HealthCheckTileServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchHealthCheckDataCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/dashboard-health-check-tile'),
        ], 'dashboard-health-check-tile-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard-health-check-tile');

        Livewire::component('health-check-tile', HealthCheckTileComponent::class);
    }
}
