<?php

return new readonly class extends \Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ReadonlyClass
{
    public function handle(): string
    {
        return \Dorayaki4369\LaravelDecorator\Facades\Decorator::handle($this, __FUNCTION__, []);
    }
};
