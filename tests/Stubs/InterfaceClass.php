<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

use Dorayaki4369\Decoravel\Tests\Stubs\Attributes\StubLogDecorator;

interface InterfaceClass
{
    #[StubLogDecorator]
    public function handle(): string;
}
