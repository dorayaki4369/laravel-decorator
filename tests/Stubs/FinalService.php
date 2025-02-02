<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

final class FinalService
{
    #[StubDecorator]
    public function handle(): string
    {
        return FinalService::class;
    }
}
