<?php

namespace Dorayaki4369\LaravelDecorator\Tests;

use Dorayaki4369\LaravelDecorator\Scanner;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ExtendedClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\InjectionRequiredClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ReadonlyClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\StandardClass;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Scanner::class)]
final class ScannerTest extends TestCase
{
    protected Scanner $scanner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scanner = app(Scanner::class);
    }

    public function test_scan(): void
    {
        $this->app['config']->set('decoravel.scan_directories', [
            __DIR__.'/Stubs',
        ]);

        $classes = $this->scanner->scanDecoratedClasses();

        $expected = [
            ExtendedClass::class,
            InjectionRequiredClass::class,
            StandardClass::class,
        ];
        if (version_compare(PHP_VERSION, '8.3.0', '>=')) {
            $expected[] = ReadonlyClass::class;
        }

        $this->assertEqualsCanonicalizing($expected, $classes);
    }
}
