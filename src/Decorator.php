<?php

namespace Dorayaki4369\LaravelDecorator;

use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;

readonly class Decorator
{
    public function __construct(
        protected Scanner $scanner,
        protected InstanceFactory $factory,
        protected DecoratorHandler $handler,
    ) {}

    /**
     * Scan classes that are decorated by LaravelDecorator.
     *
     * @return class-string[]
     */
    public function scanDecoratedClasses(): array
    {
        return $this->scanner->scanDecoratedClasses();
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $class
     * @return T
     *
     * @throws ReflectionException
     * @throws BindingResolutionException
     */
    public function decorate(string $class): object
    {
        return $this->factory->getInstance($class);
    }

    /**
     * @throws LaravelDecoratorException
     * @throws ReflectionException
     */
    public function handle(object $instance, string $method, array $args): mixed
    {
        return $this->handler->handle($instance, $method, $args);
    }
}
