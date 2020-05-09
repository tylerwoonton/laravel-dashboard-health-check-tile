<?php

namespace TylerWoonton\HealthCheckTile\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TylerWoonton\HealthCheckTile\HealthCheckStore;

class HealthCheckStoreTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @var HealthCheckStore
     */
    private $healthCheckStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthCheckStore = new HealthCheckStore();
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/../vendor/spatie/laravel-dashboard/database/migrations/create_dashboard_tiles_table.php.stub';

        (new \CreateDashboardTilesTable)->up();
    }

    /** @test */
    public function get_emoji_returns_success_emoji()
    {
        $this->assertEquals('✅', $this->healthCheckStore->getEmoji('OK'));
    }

    /** @test */
    public function get_emoji_returns_failure_emoji()
    {
        $this->assertEquals('❌', $this->healthCheckStore->getEmoji('PROBLEM'));
    }
}