<?php

namespace Dorayaki4369\Decoravel\Tests;

use Dorayaki4369\Decoravel\Decoravel;
use Dorayaki4369\Decoravel\Facades\Decoravel as Facade;

class FacadeTest extends TestCase
{
    public function testBinding(): void
    {
        $this->assertInstanceOf(Decoravel::class, Facade::getFacadeRoot());
    }
}