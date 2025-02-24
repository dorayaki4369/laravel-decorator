<?php

namespace Dorayaki4369\LaravelDecorator\Tests;

use Dorayaki4369\LaravelDecorator\Decorator;
use Dorayaki4369\LaravelDecorator\Facades\Decorator as Facade;

class FacadeTest extends TestCase
{
    public function test_binding(): void
    {
        $this->assertInstanceOf(Decorator::class, Facade::getFacadeRoot());
    }
}
