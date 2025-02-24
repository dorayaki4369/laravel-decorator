<?php

namespace Dorayaki4369\LaravelDecorator\Tests;

use Dorayaki4369\LaravelDecorator\DecoratorHandler;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ExtendedClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\InjectionRequiredClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ReadonlyClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\StandardClass;
use Generator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

#[CoversClass(DecoratorHandler::class)]
final class DecoratorHandlerTest extends TestCase
{
    protected DecoratorHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = app(DecoratorHandler::class);
    }

    #[DataProvider('dataProvider')]
    public function test_handle_standard_class(callable $callback, string $preLog, string $postLog): void
    {
        $this->assertStubLogDecorator($callback, $preLog, $postLog);
    }

    public static function dataProvider(): Generator
    {
        /** @var StandardClass $obj */
        $obj = require __DIR__.'/Stubs/Decorated/standard-class.php';

        yield 'nonModifierMethod' => [
            function () use ($obj) {
                $result = $obj->nonModifierMethod();
                $this->assertSame(StandardClass::class, $result);
            },
            StandardClass::class.'::nonModifierMethod is called',
            StandardClass::class.'::nonModifierMethod is finished',
        ];

        yield 'publicMethod' => [
            function () use ($obj) {
                $result = $obj->publicMethod();
                $this->assertInstanceOf(Application::class, $result);
            },
            StandardClass::class.'::publicMethod is called',
            StandardClass::class.'::publicMethod is finished',
        ];

        yield 'methodWithArgs' => [
            function () use ($obj) {
                $k = 2;
                $result = $obj->methodWithArgs(
                    1,
                    'string',
                    1.1,
                    true,
                    [],
                    new stdClass,
                    fn () => null,
                    [],
                    null,
                    app(),
                    $k,
                    null,
                    3,
                    false,
                    new Collection,
                    new Collection,
                    null,
                );
                $this->assertSame(StandardClass::class, $result);
                $this->assertSame(3, $k);
            },
            StandardClass::class.'::methodWithArgs is called',
            StandardClass::class.'::methodWithArgs is finished',
        ];

        yield 'methodWithVariadicArgs' => [
            function () use ($obj) {
                $result = $obj->methodWithVariadicArgs('string', 1, 2, 3);
                $this->assertSame([1, 2, 3], $result);
            },
            StandardClass::class.'::methodWithVariadicArgs is called',
            StandardClass::class.'::methodWithVariadicArgs is finished',
        ];

        yield 'methodWithVariadicReferenceArgs' => [
            function () use ($obj) {
                $a = [1, 2, 3];
                $result = $obj->methodWithVariadicReferenceArgs('string', $a[0], $a[1], $a[2]);
                $this->assertSame([2, 3, 4], $result);
            },
            StandardClass::class.'::methodWithVariadicReferenceArgs is called',
            StandardClass::class.'::methodWithVariadicReferenceArgs is finished',
        ];
    }

    public function test_handle_extended_class(): void
    {
        /** @var ExtendedClass $obj */
        $obj = require __DIR__.'/Stubs/Decorated/extended-class.php';

        $this->assertStubLogDecorator(
            function () use ($obj) {
                $result = $obj->nonModifierMethod();
                $this->assertSame(StandardClass::class, $result);
            },
            ExtendedClass::class.'::nonModifierMethod is called',
            ExtendedClass::class.'::nonModifierMethod is finished',
        );
    }

    public function test_handle_injection_required_class(): void
    {
        /** @var InjectionRequiredClass $obj */
        $obj = require __DIR__.'/Stubs/Decorated/injection-required-class.php';

        $this->assertStubLogDecorator(
            function () use ($obj) {
                $result = $obj->handle();
                $this->assertSame(InjectionRequiredClass::class, $result);
            },
            InjectionRequiredClass::class.'::handle is called',
            InjectionRequiredClass::class.'::handle is finished',
        );
    }

    public function test_handle_readonly_class(): void
    {
        if (version_compare(PHP_VERSION, '8.3.0', '<')) {
            $this->markTestSkipped('Readonly properties are available since PHP 8.3');
        }

        /** @var ReadonlyClass $obj */
        $obj = require __DIR__.'/Stubs/Decorated/readonly-class.php';

        $this->assertStubLogDecorator(
            function () use ($obj) {
                $result = $obj->handle();
                $this->assertSame(ReadonlyClass::class, $result);
            },
            ReadonlyClass::class.'::handle is called',
            ReadonlyClass::class.'::handle is finished',
        );
    }

    protected function assertStubLogDecorator(callable $callback, string $preLog, string $postLog): void
    {
        Log::spy();
        $callback->call($this);
        Log::shouldHaveReceived()->log('info', $preLog);
        Log::shouldHaveReceived()->log('info', $postLog);
        Log::clear();
    }
}
