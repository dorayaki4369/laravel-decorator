<?php

return new class extends \Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ExtendedClass
{
    public function nonModifierMethod(): string
    {
        return \Dorayaki4369\LaravelDecorator\Facades\Decorator::handle($this, __FUNCTION__, []);
    }
};
