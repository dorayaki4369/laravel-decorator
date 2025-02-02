<?php

namespace Dorayaki4369\Decoravel\Tests;

use Dorayaki4369\Decoravel\DecoravelServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as Base;

abstract class TestCase extends Base
{
    /**
     * @param  Application  $app
     * @return class-string<ServiceProvider>[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            DecoravelServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('decoravel', [
            'scan_directories' => [__DIR__.'/Stubs'],
        ]);
    }
}
