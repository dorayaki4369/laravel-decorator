<?php

namespace Dorayaki4369\LaravelDecorator\Tests;

use Dorayaki4369\LaravelDecorator\LaravelDecoratorServiceProvider;
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
            LaravelDecoratorServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('decoravel', [
            'scan_directories' => [__DIR__.'/Stubs'],
        ]);
    }
}
