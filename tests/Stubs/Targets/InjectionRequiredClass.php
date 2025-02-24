<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs\Targets;

use Dorayaki4369\Decoravel\Tests\Stubs\Attributes\StubLogDecorator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;

class InjectionRequiredClass
{
    public function __construct(
        readonly Application $app,
        public Log $log,
    ) {}

    #[StubLogDecorator]
    public function handle(): string
    {
        return self::class;
    }

    public function __destruct() {}
}
