<?php

namespace Dorayaki4369\Decoravel\Contracts\Attributes;

use Attribute;
use Illuminate\Support\Facades\Log;

#[Attribute(Attribute::TARGET_METHOD)]
class LogDecorator implements Decorator
{
    public function decorate(array $args, object $decoratable, string $method, callable $next): mixed
    {
        Log::log('info', 'Before method call');

        $result = $next($args, $decoratable, $method);

        Log::log('info', 'After method call');

        return $result;
    }
}
