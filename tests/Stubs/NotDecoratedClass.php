<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs;

class NotDecoratedClass
{
    public function handle(): string
    {
        return NotDecoratedClass::class;
    }
}
