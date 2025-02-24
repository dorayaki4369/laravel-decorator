<?php

namespace Dorayaki4369\Decoravel\Facades;

use Dorayaki4369\Decoravel\Decoravel as Concrete;
use Illuminate\Support\Facades\Facade;
use RuntimeException;

/**
 * @method static string[] scanDecoratedClasses()
 * @method static mixed decorate(string $class)
 * @method static mixed handle(object $instance, string $method, array $args)
 */
class Decoravel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     *
     * @throws RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return Concrete::class;
    }
}
