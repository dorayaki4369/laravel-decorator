<?php

namespace Dorayaki4369\Decoravel\Contracts\Attributes;

interface Decorator
{
    /**
     * Decorate the next callable.
     *
     * @param  callable(callable $next, array $args, object $instance, string $method): mixed  $next
     */
    public function decorate(callable $next, array $args, object $instance, string $method): mixed;
}
