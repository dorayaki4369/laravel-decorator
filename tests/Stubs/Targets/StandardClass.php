<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs\Targets;

use Dorayaki4369\Decoravel\Tests\Stubs\Attributes\StubLogDecorator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class StandardClass
{
    #[StubLogDecorator]
    function nonModifierMethod(): string
    {
        return self::class;
    }

    #[StubLogDecorator]
    public function publicMethod(): Application
    {
        return app();
    }

    #[StubLogDecorator]
    public function methodWithArgs(
        int $a,
        string $b,
        float $c,
        bool $d,
        array $e,
        object $f,
        callable $g,
        iterable $h,
        mixed $i,
        Application $j,
        int &$k,
        ?int $l,
        ?int $m,
        string|int|bool $n,
        Arrayable&Jsonable $o,
        (Arrayable&JsonSerializable)|(Arrayable&Jsonable) $p,
        $q,
        string $r = 'default',
    ): string {
        $k += 1;

        return self::class;
    }

    #[StubLogDecorator]
    public function methodWithVariadicArgs(string $a, int ...$args): array
    {
        return $args;
    }

    #[StubLogDecorator]
    public function methodWithVariadicReferenceArgs(string $a, int &...$args): array
    {
        foreach ($args as &$arg) {
            $arg += 1;
        }

        return $args;
    }

    // ignore following methods

    public function notDecoratedMethod(): string
    {
        return self::class;
    }

    #[StubLogDecorator]
    protected function protectedMethod(): string
    {
        return self::class;
    }

    #[StubLogDecorator]
    private function privateMethod(): string
    {
        return self::class;
    }

    #[StubLogDecorator]
    public static function staticMethod(): string
    {
        return self::class;
    }

    #[StubLogDecorator]
    final public function finalMethod(): string
    {
        return self::class;
    }

    #[StubLogDecorator]
    public function __construct() {}

    #[StubLogDecorator]
    public function __destruct() {}
}
