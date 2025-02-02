<?php

namespace Dorayaki4369\Decoravel\Contracts\Attributes;

interface Decorator
{
    /**
     * Decorate the next callable.
     *
     * @param  callable(array $args, object $decoratable, string $method, callable $next): mixed  $next
     */
    public function decorate(array $args, object $decoratable, string $method, callable $next): mixed;
}
