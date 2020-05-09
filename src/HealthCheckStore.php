<?php

namespace TylerWoonton\HealthCheckTile;

use Spatie\Dashboard\Models\Tile;

class HealthCheckStore
{
    const SUCCESS_EMOJI = "✅";

    const FAILURE_EMOJI = "❌";

    private Tile $tile;

    public static function make()
    {
        return new static();
    }

    public function __construct()
    {
        $this->tile = Tile::firstOrCreateForName('health-check');
    }

    public function setHealthCheck(array $healthCheck): self
    {
        $this->tile->putData('healthCheck', $healthCheck);

        return $this;
    }

    public function healthCheck(): array
    {
        return $this->tile->getData('healthCheck') ?? [];
    }

    public function setFailures(array $failures): self
    {
        $this->tile->putData('failures', $failures);

        return $this;
    }

    public function failures(): array
    {
        return $this->tile->getData('failures') ?? [];
    }

    public function getEmoji($status): string
    {
        return ($status == 'OK') ? self::SUCCESS_EMOJI : self::FAILURE_EMOJI;
    }
}
