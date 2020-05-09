<?php

namespace TylerWoonton\HealthCheckTile\Tests\Services;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Http;
use TylerWoonton\HealthCheckTile\Services\HealthCheckService;

class HealthCheckServiceTest extends TestCase
{
    /**
     * @var HealthCheckService
     */
    private $healthCheckService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthCheckService = new HealthCheckService();
        
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

    /** @test */
    public function successful_response_is_requested_correctly()
    {
        $response = $this->healthCheckService::getHealth('pass.com/health');

        $this->assertEquals('OK', $response['status']);
    }

    /** @test */
    public function problem_response_is_requested_correctly()
    {
        $response = $this->healthCheckService::getHealth('fail.com/health');

        $this->assertEquals('PROBLEM', $response['status']);
    }
}