<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs;

use Dorayaki4369\LaravelDecorator\Tests\Stubs\Attributes\StubLogDecorator;

interface InterfaceClass
{
    #[StubLogDecorator]
    public function handle(): string;
}
