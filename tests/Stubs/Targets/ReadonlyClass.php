<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets;

use Dorayaki4369\LaravelDecorator\Tests\Stubs\Attributes\StubLogDecorator;

readonly class ReadonlyClass
{
    #[StubLogDecorator]
    public function handle(): string
    {
        return ReadonlyClass::class;
    }
}
