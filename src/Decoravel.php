<?php

namespace Dorayaki4369\Decoravel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

readonly class Decoravel
{
    public function __construct(
        protected Scanner $scanner,
        protected InstanceFactory $factory,
        protected DecoratorHandler $handler,
    ) {}

    /**
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
     * @throws DecoravelException
     * @throws ReflectionException
     */
    public function handle(object $instance, string $method, array $args): mixed
    {
        return $this->handler->handle($instance, $method, $args);
    }
}
