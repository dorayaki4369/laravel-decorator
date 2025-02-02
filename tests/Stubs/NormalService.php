<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

use Illuminate\Contracts\Foundation\Application;

class NormalService
{
    public function __construct(
        readonly Application $app,
    ) {}

    #[StubDecorator]
    public function handle(): string
    {
        return FakeService::class;
    }

    public function __destruct() {}
}
