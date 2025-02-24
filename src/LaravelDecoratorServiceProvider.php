<?php

namespace Dorayaki4369\LaravelDecorator;

use Dorayaki4369\LaravelDecorator\Facades\Decorator as LaravelDecoratorFacade;
use Illuminate\Support\ServiceProvider;

class LaravelDecoratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/decoravel.php', 'decoravel');

        $this->app->singleton(LaravelDecoratorFacade::class, Decorator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootInConsole();
        }

        $this->bindDecoratedClasses();
    }

    /**
     * Only booting in console.
     */
    protected function bootInConsole(): void
    {
        $this->publishes(
            [
                __DIR__.'/../config/decoravel.php' => config_path('decoravel.php'),
            ],
            'decoravel-config'
        );
    }

    protected function bindDecoratedClasses(): void
    {
        foreach (LaravelDecoratorFacade::scanDecoratedClasses() as $class) {
            $this->app->bind($class, fn () => LaravelDecoratorFacade::decorate($class));
        }
    }
}
