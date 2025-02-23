<?php

return new readonly class extends \Dorayaki4369\Decoravel\Tests\Stubs\Targets\ReadonlyClass
{
    public function handle(): string
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, 'handle');
    }
};