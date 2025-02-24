<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs;

use Dorayaki4369\LaravelDecorator\Tests\Stubs\Attributes\StubLogDecorator;

trait TraitClass
{
    #[StubLogDecorator]
    public function run(): string
    {
        return self::class;
    }
}
