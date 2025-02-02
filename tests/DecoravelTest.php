<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Dorayaki4369\Decoravel\Tests;

use Dorayaki4369\Decoravel\Decoravel;
use Dorayaki4369\Decoravel\Tests\Stubs\NormalService;
use Dorayaki4369\Decoravel\Tests\Stubs\ReadonlyService;
use Generator;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

class DecoravelTest extends TestCase
{
    protected Decoravel $decoravel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->decoravel = app(Decoravel::class);
    }

    public function test_scan_decoratable_classes(): void
    {
        $classes = $this->decoravel->scanDecoratableClasses();

        $this->assertCount(2, $classes);
        $this->assertContains(NormalService::class, $classes);
        $this->assertContains(ReadonlyService::class, $classes);
    }

    #[DataProvider('decorateDataProvider')]
    public function test_decorate(string $class): void
    {
        $object = $this->decoravel->decorate($class);

        $ref = new ReflectionClass($object);
        $this->assertTrue($ref->isAnonymous());

        $this->assertInstanceOf($class, $object);
    }

    public static function decorateDataProvider(): Generator
    {
        yield 'NormalService' => [
            NormalService::class,
        ];

        //        yield 'ReadonlyService' => [
        //            ReadonlyService::class,
        //        ];
    }

    public function test_handle(): void
    {
        Log::partialMock()
            ->shouldReceive('log')
            ->once()
            ->with('info', 'StubDecorator is called');

        Log::partialMock()
            ->shouldReceive('log')
            ->once()
            ->with('info', 'StubDecorator is finished');

        $object = $this->decoravel->decorate(NormalService::class);

        $result = $object->handle();
        $this->assertSame(NormalService::class, $result);
    }
}
