<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

use Attribute;
use Dorayaki4369\Decoravel\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\Log;

#[Attribute(Attribute::TARGET_METHOD)]
class StubDecorator implements Decorator
{
    public function decorate(array $args, object $decoratable, string $method, callable $next): mixed
    {
        Log::log('info', 'StubDecorator is called');

        $result = $next($args, $decoratable, $method);

        Log::log('info', 'StubDecorator is finished');

        return $result;
    }
}
