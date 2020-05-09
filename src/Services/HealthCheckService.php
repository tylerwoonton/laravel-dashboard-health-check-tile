<?php

namespace TylerWoonton\HealthCheckTile\Services;

use Illuminate\Support\Facades\Http;

class HealthCheckService
{
    public static function getHealth(string $url, $headers = [], $options = [], $timeout = 3)
    {
        return Http::timeout($timeout)
                    ->withHeaders($headers)
                    ->withOptions($options)
                    ->get($url)
                    ->json();
    }
}
