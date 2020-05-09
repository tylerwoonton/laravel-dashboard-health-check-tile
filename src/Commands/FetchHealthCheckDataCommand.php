<?php

namespace TylerWoonton\HealthCheckTile\Commands;

use Illuminate\Console\Command;
use TylerWoonton\HealthCheckTile\HealthCheckStore;
use TylerWoonton\HealthCheckTile\Services\HealthCheckService;

class FetchHealthCheckDataCommand extends Command
{
    protected $signature = 'dashboard:fetch-health-check-data';

    protected $description = 'Fetch Health Check Data';

    private $healthCheckStore;

    public function __construct()
    {
        $this->healthCheckStore = HealthCheckStore::make();

        parent::__construct();
    }

    public function handle()
    {
        $healthCheck = [];
        $failures = [];

        foreach (config('dashboard.tiles.health_check.sites') as $siteName => $siteConfig) {
            $check = HealthCheckService::getHealth(
                $siteConfig['url'],
                $siteConfig['headers'] ?? [],
                $siteConfig['options'] ?? [],
                config('dashboard.tiles.health_check.timeout', 3)
            ) ?? $this->default_failure_response();
            
            $healthCheck[$siteName] = [
                "status" => $check['status'],
                "emoji" => $this->healthCheckStore->getEmoji($check['status']),
                "response" => $check
            ];

            foreach (array_keys($check) as $checkName) {
                if ($checkName == 'status') {
                    continue;
                }

                if ($check[$checkName]['status'] == 'OK') {
                    $failures[$siteName] = [];
                    continue;
                }

                $failures[$siteName][$checkName] = [
                    'message' => $check[$checkName]['message']
                ];
            }
        }

        $this->healthCheckStore->setHealthCheck($healthCheck);
        $this->healthCheckStore->setFailures($failures);

        $this->info('Fetched Health Check Data!');
    }

    private function default_failure_response()
    {
        return [
            'status' => 'PROBLEM',
            'ERROR' => [
                'status' => 'PROBLEM',
                'message' => 'URL could not be resolved.'
            ]
        ];
    }
}
