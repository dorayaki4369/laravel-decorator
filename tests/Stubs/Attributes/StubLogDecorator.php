<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs\Attributes;

use Attribute;
use Dorayaki4369\Decoravel\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\Log;

#[Attribute(Attribute::TARGET_METHOD)]
class StubLogDecorator implements Decorator
{
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed
    {
        Log::log('info', "$parentClass::$method is called");

        $result = $next($args, $instance, $method);

        Log::log('info', "$parentClass::$method is finished");

        return $result;
    }
}
