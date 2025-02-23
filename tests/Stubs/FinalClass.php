<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

use Dorayaki4369\Decoravel\Tests\Stubs\Attributes\StubLogDecorator;

final class FinalClass
{
    #[StubLogDecorator]
    public function handle(): string
    {
        return FinalClass::class;
    }
}
