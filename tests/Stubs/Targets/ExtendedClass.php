<?php

namespace Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets;

class ExtendedClass extends StandardClass
{
    public function nonModifierMethod(): string
    {
        return parent::nonModifierMethod();
    }
}
