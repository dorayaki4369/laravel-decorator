<?php

namespace Dorayaki4369\Decoravel;

use Dorayaki4369\Decoravel\Contracts\Attributes\Decorator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

readonly class DecoratorHandler
{
    /**
     * @throws DecoravelException
     * @throws ReflectionException
     */
    public function handle(object $instance, string $method, array $args): mixed
    {
        $parent = (new ReflectionClass($instance))->getParentClass();
        if ($parent === false) {
            throw new DecoravelException('Parent class not found');
        }

        $parentMethod = $parent->getMethod($method);
        $invoker = $this->makeParentMethodInvoker($parentMethod, $args);

        $attrs = getDecoratorAttributes($parentMethod);
        if (empty($attrs)) {
            return $invoker($instance);
        }

        $fn = array_reduce($attrs, function (callable $fn, ReflectionAttribute $attr) {
            /** @var Decorator $decorator */
            $decorator = $attr->newInstance();

            return fn (array $args, object $instance, string $parentClass, string $method) => $decorator->decorate($fn, $args, $instance, $parentClass, $method);
        }, fn ($args, $instance) => $invoker($instance));

        return $fn($args, $instance, $parent->getName(), $method);
    }

    protected function makeParentMethodInvoker(ReflectionMethod $ref, array $args): callable
    {
        $params = $ref->getParameters();

        $newArgs = [];
        foreach ($params as $i => $param) {
            if ($param->isVariadic()) {
                $newArgs = array_merge($newArgs, array_slice($args, $i));
                break;
            }

            if ($param->isPassedByReference()) {
                $newArgs[] = &$args[$i];
            } else {
                $newArgs[] = $args[$i] ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
            }
        }

        return fn (object $instance) => $ref->invokeArgs($instance, $newArgs);
    }
}
