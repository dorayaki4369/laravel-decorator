<?php

namespace Dorayaki4369\Decoravel;

use Dorayaki4369\Decoravel\Facades\Decoravel as DecoravelFacade;
use Illuminate\Support\ServiceProvider;

class DecoravelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/decoravel.php', 'decoravel');

        $this->app->singleton(DecoravelFacade::class, Decoravel::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootInConsole();
        }

        $this->bindDecoratables();
    }

    protected function bootInConsole(): void
    {
        $this->publishes(
            [
                __DIR__.'/../config/decoravel.php' => config_path('decoravel.php'),
            ],
            'decoravel-config'
        );
    }

    protected function bindDecoratables(): void
    {
        foreach (DecoravelFacade::scanDecoratedClasses() as $class) {
            $this->app->bind($class, fn () => DecoravelFacade::decorate($class));
        }
    }
}
