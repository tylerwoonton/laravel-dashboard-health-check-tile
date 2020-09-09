# Health Check Tile for Laravel Dashboard

![Packagist Version](https://img.shields.io/packagist/v/tylerwoonton/laravel-dashboard-health-check-tile?color=%234299E1&style=for-the-badge)
[![GitHub license](https://img.shields.io/github/license/tylerwoonton/laravel-dashboard-health-check-tile?color=%234299E1&style=for-the-badge)](https://github.com/tylerwoonton/laravel-dashboard-health-check-tile/blob/master/LICENCE)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/tylerwoonton/laravel-dashboard-health-check-tile/Run%20tests?color=4299E1&style=for-the-badge)

[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen?style=for-the-badge)](https://plant.treeware.earth/tylerwoonton/laravel-dashboard-health-check-tile)

The purpose of this package is to integrate the [ukfast/laravel-health-check](https://github.com/ukfast/laravel-health-check) package into a tile for [spatie/laravel-dashboard](https://github.com/spatie/laravel-dashboard).

![Example Screenshot](https://raw.githubusercontent.com/tylerwoonton/laravel-dashboard-health-check-tile/master/docs/example.png)

## Installation

This package requires the [ukfast/laravel-health-check](https://github.com/ukfast/laravel-health-check) package to be running on the endpoint provided so we can assert that your services are working. Please follow the instructions in that repo to install the package.

You can install the package via composer:

```bash
composer require tylerwoonton/laravel-dashboard-health-check-tile
```

## Configuration

In the `dashboard` config file, you must add this configuration in the `tiles` key. 

The `sites` array should contain an array of sites with their respective URLs and any custom `headers` or Guzzle `options` that need to be executed when hitting the URL.

The `timeout` option is the Guzzle request timeout in seconds. This is, by default, 3 seconds per request.

The `show_failures` option determines whether the services failing are displayed on the tile. If you only want to see concise, overall statuses it's fine to disable this.

The `refresh_interval` option determines how many seconds will pass before the dashboard tile is re-rendered.

```php
// config/dashboard.php

return [
    // ...
    'tiles' => [
        'health_check' => [
            'sites' => [
                'Example App' => [
                    "url" => 'https://example.app/health', 
                    "headers" => [], // optional
                    "options" => [] // optional
                ]
            ],
            'timeout' => 3,
            'show_failures' => true,
            'refresh_interval' => 60
        ],
    ],
];
```

In `app\Console\Kernel.php` you should schedule the `\TylerWoonton\HealthCheckTile\Commands\FetchHealthCheckDataCommand` to run. You can let it run every minute if you want. You could also run it less frequently if fast updates on the dashboard aren't that important for this tile.

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command(\TylerWoonton\HealthCheckTile\Commands\FetchHealthCheckDataCommand::class)->everyMinute();
}
```

## Usage

In your dashboard view you use the `livewire:health-check-tile` component. 

```html
<x-dashboard>
    <livewire:health-check-tile position="a1" />
</x-dashboard>
```

### Customising the view

If you want to customise the view used to render this tile, run this command:

```bash
php artisan vendor:publish --provider="TylerWoonton\HealthCheckTile\HealthCheckTileServiceProvider" --tag="dashboard-health-check-tile-views"
```

## Testing

``` bash
composer test
```

## Contributing

We welcome contributions to this package. All new changes should be well-tested and follow [PSR-12](https://www.php-fig.org/psr/psr-12/) standards.

Please refer to our [CONTRIBUTING](CONTRIBUTING.md) file for more information.

## Treeware

This package is [**Treeware**](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/tylerwoonton/laravel-dashboard-health-check-tile) to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.