<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

interface InterfaceService
{
    #[StubDecorator]
    public function handle(): string;
}
