<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs;

use Illuminate\Contracts\Foundation\Application;

readonly class ReadonlyService
{
    public function __construct(
        public Application $app,
    ) {}

    #[StubDecorator]
    public function handle(): string
    {
        return ReadonlyService::class;
    }
}
