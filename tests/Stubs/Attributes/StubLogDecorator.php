<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs\Attributes;

use Attribute;
use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\Log;

#[Attribute(Attribute::TARGET_METHOD)]
class StubLogDecorator implements Decorator
{
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed
    {
        Log::log('info', "$parentClass::$method is called");

        $result = $next($args, $instance, $parentClass, $method);

        Log::log('info', "$parentClass::$method is finished");

        return $result;
    }
}
