<?php

$app = (function () {})();

return new class($app) extends \Dorayaki4369\Decoravel\Tests\Stubs\StubService
{
    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        parent::__construct($app);
    }

    public function handle(): string
    {
        return \Dorayaki4369\Decoravel\Facades\Decoravel::handle($this, 'handle', []);
    }
};
