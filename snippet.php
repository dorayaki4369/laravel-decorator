<?php

$args = (function () use ($class) {
    $method = (new ReflectionClass($class))->getConstructor();
    if (null === $method) {
        return [];
    }

    return array_map(function (ReflectionParameter $p) {
        $class = $p->getType()?->getName();
        return $class ? app($class) : null;
    }, $method->getParameters());
})();