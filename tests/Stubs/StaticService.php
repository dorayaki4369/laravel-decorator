<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

class StaticService
{
    #[StubDecorator]
    public static function handle(): string
    {
        return StaticService::class;
    }
}
