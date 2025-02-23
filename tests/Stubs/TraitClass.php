<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

use Dorayaki4369\Decoravel\Tests\Stubs\Attributes\StubLogDecorator;

trait TraitClass
{
    #[StubLogDecorator]
    public function run(): string
    {
        return self::class;
    }
}
