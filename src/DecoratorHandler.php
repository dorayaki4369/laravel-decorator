<?php

namespace Dorayaki4369\Decoravel;

use Dorayaki4369\Decoravel\Contracts\Attributes\Decorator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class DecoratorHandler
{
    /**
     * @param  object|class-string  $instance
     *
     * @throws ReflectionException
     */
    public function handle(object|string $instance, string $method, mixed ...$args): mixed
    {
        $ref = new ReflectionClass($instance);
        $methodRef = $ref->getMethod($method);

        $attrs = getDecoratorAttributes($methodRef);
        if (empty($attrs)) {
            return $instance->$method(...$args);
        }

        $fn = array_reduce($attrs, function (callable $fn, ReflectionAttribute $attr) {
            /** @var Decorator $decorator */
            $decorator = $attr->newInstance();

            return fn (array $args, object $instance, string $method) => $decorator->decorate($fn, $args, $instance, $method);
        }, fn (array $args, object $instance, string $method) => $instance->$method(...$args));

        return $fn($args, $instance, $method);
    }
}
