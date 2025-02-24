<?php

$method = (new \ReflectionClass(\Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\InjectionRequiredClass::class))->getConstructor();
if ($method === null) {
    $args = [];
} else {
    $args = array_map(function ($p) {
        $class = $p->getType()?->getName();

        return $class ? app($class) : null;
    }, $method->getParameters());
}

return new class(...$args) extends \Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\InjectionRequiredClass
{
    public function handle(): string
    {
        return \Dorayaki4369\LaravelDecorator\Facades\Decorator::handle($this, __FUNCTION__, []);
    }
};
