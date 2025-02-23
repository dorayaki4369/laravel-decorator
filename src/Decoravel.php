<?php

namespace Dorayaki4369\Decoravel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

readonly class Decoravel
{
    public function __construct(
        protected InstanceFactory $factory,
        protected DecoratorHandler $handler,
    ) {}

    /**
     * @template T
     *
     * @return class-string<T>[]
     */
    public function scanDecoratableClasses(): array
    {
        $classes = $this->scanClasses();

        return array_values(array_filter(array_map(function (string $class): ?string {
            try {
                $ref = new ReflectionClass($class);

                if (! isDecoratableClass($ref)) {
                    return null;
                }

                return $class;
            } catch (ReflectionException) {
                return null;
            }
        }, $classes)));
    }

    /**
     * @return class-string[]
     */
    protected function scanClasses(): array
    {
        $phpFiles = Finder::create()
            ->files()
            ->name('*.php')
            ->in(config('decoravel.scan_directories', []))
            ->getIterator();

        $classes = [];
        foreach ($phpFiles as $info) {
            $class = ClassMapGenerator::createMap($info->getPathname());
            if (empty($class)) {
                continue;
            }

            $classes[] = array_keys($class)[0];
        }

        return $classes;
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $class
     * @return T
     *
     * @throws ReflectionException
     */
    public function decorate(string $class): mixed
    {
        return $this->factory->getInstance($class);
    }

    /**
     * @param  object|class-string  $instance
     * @param  mixed  ...$args
     *
     * @throws ReflectionException
     */
    public function handle(object|string $instance, string $method, array ...$args): mixed
    {
        return $this->handler->handle($instance, $method, ...$args);
    }
}
