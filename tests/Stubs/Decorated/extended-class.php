<?php

return new class extends \Dorayaki4369\Decoravel\Tests\Stubs\Targets\ExtendedClass
{
    public function nonModifierMethod(): string
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, __FUNCTION__, []);
    }
};
