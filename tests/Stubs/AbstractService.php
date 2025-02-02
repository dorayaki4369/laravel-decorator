<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

abstract class AbstractService
{
    #[StubDecorator]
    public function handle(): string
    {
        return AbstractService::class;
    }
}
