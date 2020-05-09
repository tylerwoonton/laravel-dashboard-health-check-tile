<?php

namespace TylerWoonton\HealthCheckTile;

use Livewire\Component;
use TylerWoonton\HealthCheckTile\HealthCheckStore;

class HealthCheckTileComponent extends Component
{
    /**
     * @var string
     */
    public $position;

    public function mount(string $position)
    {
        $this->position = $position;
    }

    public function render()
    {
        $healthCheckStore = HealthCheckStore::make();

        return view('dashboard-health-check-tile::tile', [
            'healthCheck' => $healthCheckStore->healthCheck(),
            'failures' => $healthCheckStore->failures(),
            'refreshIntervalInSeconds' => config('dashboard.tiles.health_check.refresh_interval', 60)
        ]);
    }
}
