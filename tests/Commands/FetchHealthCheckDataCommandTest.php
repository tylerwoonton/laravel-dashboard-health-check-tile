<?php

namespace TylerWoonton\HealthCheckTile\Tests\Commands;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TylerWoonton\HealthCheckTile\HealthCheckStore;

class FetchHealthCheckCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Http::fake([
            // Stub response for OK check
            'pass.com/health' => Http::response([
                "status" => "OK",
                "log" => [
                    "status" => "OK"
                ],
                "database" => [
                    "status" => "OK"
                ],
                "env" => [
                    "status" => "OK"
                ]
            ], 200),
        
            // Stub response for PROBLEM check
            'fail.com/health' => Http::response([
                "status" => "PROBLEM",
                "log" => [
                    "status" => "OK"
                ],
                "database" => [
                    "status" => "OK"
                ],
                "env" => [
                    "status" => "PROBLEM",
                    "message" => "Missing env params",
                    "context" => [
                        "missing" => [
                            "TEST"
                        ]
                    ]
                ]
            ], 500),
        ]);
    }

    public function getPackageProviders($app)
    {
        return [
            'TylerWoonton\HealthCheckTile\HealthCheckTileServiceProvider',
            'Livewire\LivewireServiceProvider'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/../../vendor/spatie/laravel-dashboard/database/migrations/create_dashboard_tiles_table.php.stub';

        (new \CreateDashboardTilesTable)->up();
    }

    /** @test */
    public function successful_response_can_be_parsed()
    {
        config(['dashboard.tiles.health_check' => [
            'sites' => [
                'Passing Example' => [
                    "url" => 'http://pass.com/health', 
                ],
            ],
            'timeout' => 3,
            'show_failures' => true,
            'refresh_interval' => 60
        ]]);

        $this->artisan('dashboard:fetch-health-check-data')->assertExitCode(0);

        $this->assertEquals('OK', (new HealthCheckStore())->healthCheck()['Passing Example']['status']);
        $this->assertEmpty((new HealthCheckStore())->failures()['Passing Example']);
    }

    /** @test */
    public function problem_response_can_be_parsed()
    {
        config(['dashboard.tiles.health_check' => [
            'sites' => [
                'Failing Example' => [
                    "url" => 'http://fail.com/health', 
                ]
            ],
            'timeout' => 3,
            'show_failures' => true,
            'refresh_interval' => 60
        ]]);

        $this->artisan('dashboard:fetch-health-check-data')->assertExitCode(0);
        
        $this->assertEquals('PROBLEM', (new HealthCheckStore())->healthCheck()['Failing Example']['status']);
        $this->assertNotEmpty((new HealthCheckStore())->failures()['Failing Example']);
    }

    /** @test */
    public function unreachable_url_is_handled()
    {
        config(['dashboard.tiles.health_check' => [
            'sites' => [
                'Unreachable Example' => [
                    "url" => 'http://broken.com/health', 
                ]
            ],
            'timeout' => 0.1,
            'show_failures' => true,
            'refresh_interval' => 60
        ]]);

        $this->artisan('dashboard:fetch-health-check-data')->assertExitCode(0);
        
        $this->assertEquals('PROBLEM', (new HealthCheckStore())->healthCheck()['Unreachable Example']['status']);
        $this->assertNotEmpty((new HealthCheckStore())->failures()['Unreachable Example']);
    }
}