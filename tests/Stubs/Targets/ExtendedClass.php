<?php

namespace Dorayaki4369\Decoravel\Tests\Stubs\Targets;

class ExtendedClass extends StandardClass
{
    public function nonModifierMethod(): string
    {
        return parent::nonModifierMethod();
    }
}
