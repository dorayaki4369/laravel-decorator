<?php

use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;

/**
 * Find all decorated methods in the given class.
 *
 * @return ReflectionMethod[]
 */
function getDecoratedMethods(ReflectionClass $ref): array
{
    return array_filter($ref->getMethods(), 'isDecoratedMethod');
}

/**
 * Check if the given method is decorated.
 *
 * @throws ReflectionException
 */
function isDecoratedMethod(ReflectionMethod $ref): bool
{
    return count(getDecoratorAttributes($ref)) > 0;
}

/**
 * Get all decorator attributes of the given method.
 *
 * The attributes of the ancestor classes will also be included.
 *
 * @return ReflectionAttribute<Decorator>[]
 *
 * @throws ReflectionException
 */
function getDecoratorAttributes(ReflectionMethod $ref): array
{
    if (! isDecoratableMethod($ref)) {
        return [];
    }

    $attrs = $ref->getAttributes(Decorator::class, ReflectionAttribute::IS_INSTANCEOF);

    $parent = $ref->getDeclaringClass()->getParentClass();
    if ($parent && $parent->hasMethod($ref->getName())) {
        $attrs = [
            ...$attrs,
            ...$parent->getMethod($ref->getName())->getAttributes(Decorator::class, ReflectionAttribute::IS_INSTANCEOF),
        ];
    }

    return $attrs;
}

/**
 * Check if the given class is decoratable.
 */
function isDecoratableClass(ReflectionClass $ref): bool
{
    $result = ! $ref->isAbstract() && ! $ref->isInterface() && ! $ref->isTrait() && ! $ref->isFinal();
    if (! $result) {
        return false;
    }

    $is83 = version_compare(PHP_VERSION, '8.3.0', '>=');
    if (! $is83 && $ref->isReadonly()) {
        return false;
    }

    return true;
}

/**
 * Check if the given method is decoratable.
 */
function isDecoratableMethod(ReflectionMethod $ref): bool
{
    return $ref->isPublic() && ! $ref->isStatic() && ! $ref->isFinal() && ! $ref->isConstructor() && ! $ref->isDestructor();
}
