<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs;

use Dorayaki4369\LaravelDecorator\Tests\Stubs\Attributes\StubLogDecorator;

abstract class AbstractClass
{
    #[StubLogDecorator]
    public function handle(): string
    {
        return AbstractClass::class;
    }
}
