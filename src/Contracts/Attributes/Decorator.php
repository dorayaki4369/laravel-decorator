<?php

namespace Dorayaki4369\Decoravel\Contracts\Attributes;

interface Decorator
{
    /**
     * Decorate the original method.
     *
     * @param  callable(array $args, object $instance, string $parentClass, string $method): mixed  $next Next decorator method or original method invoker
     * @param  class-string  $parentClass
     */
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed;
}
