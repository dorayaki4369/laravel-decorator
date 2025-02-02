<?php

namespace Dorayaki4369\Decoravel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Dorayaki4369\Decoravel\Contracts\Attributes\Decorator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Symfony\Component\Finder\Finder;

class Decoravel
{
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

                if (
                    $ref->isAbstract() ||
                    $ref->isInterface() ||
                    $ref->isTrait() ||
                    $ref->isFinal()
                ) {
                    return null;
                }

                $methods = $this->getDecoratableMethods($ref);
                if (empty($methods)) {
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
            $classes[] = array_keys($class)[0];
        }

        return $classes;
    }

    /**
     * @return ReflectionMethod[]
     */
    protected function getDecoratableMethods(ReflectionClass $ref): array
    {
        $publicMethods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($publicMethods, fn (ReflectionMethod $method) => ! $method->isStatic() &&
            ! $method->isConstructor() &&
            ! $method->isDestructor()
        );

        return array_filter($methods, fn (ReflectionMethod $method) => count($this->getDecoratorAttributes($method)) > 0);
    }

    /**
     * @return ReflectionAttribute[]
     */
    protected function getDecoratorAttributes(ReflectionMethod $method): array
    {
        return $method->getAttributes(Decorator::class, ReflectionAttribute::IS_INSTANCEOF);
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
        $ref = new ReflectionClass($class);

        $methods = $this->getDecoratableMethods($ref);
        if (empty($methods)) {
            return app($class);
        }

        $code = $this->createCode($class, $ref, $methods);

        if ($const = $ref->getConstructor()) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $args = $this->resolveConstructorParameters($const);
        }

        return eval($code);
    }

    /**
     * @param  ReflectionMethod[]  $methods
     */
    protected function createCode(string $class, ReflectionClass $ref, array $methods): string
    {
        assert(count($methods) > 0);

        $methodStrList = array_map(fn (ReflectionMethod $method) => $this->createMethodStr($method), $methods);
        $methodsStr = implode(PHP_EOL, $methodStrList);

        if ($const = $ref->getConstructor()) {
            $constructorStr = $this->createConstructorStr($const);

            return <<<PHP
                return new class(...\$args) extends $class {
                    $constructorStr

                    $methodsStr
                };
            PHP;
        }

        return <<<PHP
            return new class extends $class {
                $methodsStr
            };
        PHP;
    }

    protected function resolveConstructorParameters(ReflectionMethod $const): array
    {
        $constructParameters = $const->getParameters();

        return array_map(function (ReflectionParameter $p) {
            $class = $p->getType()?->getName();

            return $class ? app($class) : null;
        }, $constructParameters);
    }

    protected function createConstructorStr(ReflectionMethod $method): string
    {
        assert($method->isConstructor());

        $params = $this->createMethodParametersStr($method);
        $args = $this->createMethodArgsStr($method);

        return <<<PHP
public function __construct($params) {
    parent::__construct($args);
}
PHP;
    }

    protected function createMethodStr(ReflectionMethod $method): string
    {
        assert(! $method->isConstructor());

        $name = $method->getName();
        $params = $this->createMethodParametersStr($method);
        $return = $this->createMethodReturnStr($method);
        $args = $this->createMethodArgsStr($method);
        $args = $args === '' ? '[]' : $args;

        return <<<PHP
public function $name($params)$return {
    return \Dorayaki4369\Decoravel\Facades\Decoravel::handle(\$this, '$name', $args);
}
PHP;
    }

    protected function createMethodParametersStr(ReflectionMethod $method): string
    {
        $params = [];
        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            $typeStr = $this->createTypeStr($type);

            $definition = "$typeStr \${$param->getName()}";
            if ($param->isDefaultValueAvailable()) {
                $definition .= ' = '.var_export($param->getDefaultValue(), true);
            }

            $params[] = $definition;
        }

        return implode(', ', $params);
    }

    protected function createMethodArgsStr(ReflectionMethod $method): string
    {
        return implode(', ', array_map(fn ($p) => '$'.$p->getName(), $method->getParameters()));
    }

    protected function createMethodReturnStr(ReflectionMethod $method): string
    {
        $type = $method->getReturnType();
        $str = $this->createTypeStr($type);

        return $str !== '' ? ": $str" : '';
    }

    protected function createTypeStr(?ReflectionType $type): string
    {
        if ($type === null) {
            return '';
        }

        if ($type instanceof ReflectionNamedType) {
            return ($type->allowsNull() ? '?' : '').$type->getName();
        }

        if ($type instanceof ReflectionIntersectionType) {
            return $this->createCompositeTypeStr($type, '&');
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->createCompositeTypeStr($type, '|');
        }

        return '';
    }

    protected function createCompositeTypeStr(ReflectionIntersectionType|ReflectionUnionType $type, string $separator): string
    {
        return implode($separator, array_filter(array_map(
            static function (ReflectionType $t) {
                return method_exists($t, 'getName') ? $t->getName() : '';
            },
            $type->getTypes()
        )));
    }

    /**
     * @throws ReflectionException
     */
    public function handle(object $decoratable, string $method, array $args): mixed
    {
        $ref = new ReflectionClass($decoratable);
        $methodRef = $ref->getMethod($method);

        $attrs = $this->getDecoratorAttributes($methodRef);
        if (empty($attrs)) {
            return $decoratable->$method(...$args);
        }

        $fn = array_reduce($attrs, function (callable $fn, ReflectionAttribute $attr) {
            /** @var Decorator $decorator */
            $decorator = $attr->newInstance();

            return fn (array $args, object $decoratable, string $method) => $decorator->decorate($args, $decoratable, $method, $fn);
        }, fn (array $args, object $decoratable, string $method) => $decoratable->$method(...$args));

        return $fn($args, $decoratable, $method);
    }
}
