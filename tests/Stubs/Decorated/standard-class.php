<?php

return new class extends \Dorayaki4369\Decoravel\Tests\Stubs\Targets\StandardClass
{
    public function nonModifierMethod(): string
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, __FUNCTION__, []);
    }

    public function publicMethod(): \Illuminate\Contracts\Foundation\Application
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, __FUNCTION__, []);
    }

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
        \Illuminate\Contracts\Foundation\Application $j,
        int &$k,
        ?int $l,
        ?int $m,
        string|int|bool $n,
        \Illuminate\Contracts\Support\Arrayable&\Illuminate\Contracts\Support\Jsonable $o,
        (\Illuminate\Contracts\Support\Arrayable&\JsonSerializable)|(\Illuminate\Contracts\Support\Arrayable&\Illuminate\Contracts\Support\Jsonable) $p,
        $q,
        string $r = 'default',
    ): string {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, __FUNCTION__, [$a, $b, $c, $d, $e, $f, $g, $h, $i, $j, &$k, $l, $m, $n, $o, $p, $q, $r]);
    }

    public function methodWithVariadicArgs(string $a, int ...$args): array
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, __FUNCTION__, [$a, ...$args]);
    }

    public function methodWithVariadicReferenceArgs(string $a, int &...$args): array
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, __FUNCTION__, [$a, ...$args]);
    }
};
