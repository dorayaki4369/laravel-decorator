<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Dorayaki4369\LaravelDecorator\Tests;

use Dorayaki4369\LaravelDecorator\InstanceFactory;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\AbstractClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\FinalClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\InterfaceClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\NotDecoratedClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\InjectionRequiredClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\ReadonlyClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\StandardClass;
use Dorayaki4369\LaravelDecorator\Tests\Stubs\TraitClass;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionException;
use Throwable;

#[CoversClass(InstanceFactory::class)]
final class InstanceFactoryTest extends TestCase
{
    protected InstanceFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = app(InstanceFactory::class);
    }

    #[DataProvider('dataProvider')]
    public function test_get_instance(string $class): void
    {
        $object = $this->factory->getInstance($class);

        $this->assertInstanceOf($class, $object);
        $this->assertTrue((new ReflectionClass($object))->isAnonymous());
    }

    #[DataProvider('dataProvider')]
    public function test_generate_code(string $class, string $expectFilePath): void
    {
        $expected = file_get_contents($expectFilePath);
        $actual = $this->factory->generateCode($class);

        $parser = (new ParserFactory)->createForHostVersion();
        $expectedStmts = $parser->parse($expected);
        $actualStmts = $parser->parse('<?php '.$actual);

        $printer = new PrettyPrinter\Standard;
        $expectedCode = $printer->prettyPrintFile($expectedStmts);
        $actualCode = $printer->prettyPrintFile($actualStmts);

        $this->assertSame($expectedCode, $actualCode);
    }

    public static function dataProvider(): Generator
    {
        yield NotDecoratedClass::class => [
            NotDecoratedClass::class,
            realpath(__DIR__.'/Stubs/Decorated/not-decorated-class.php'),
        ];

        yield StandardClass::class => [
            StandardClass::class,
            realpath(__DIR__.'/Stubs/Decorated/standard-class.php'),
        ];

        yield InjectionRequiredClass::class => [
            InjectionRequiredClass::class,
            realpath(__DIR__.'/Stubs/Decorated/injection-required-class.php'),
        ];

        $is83 = version_compare(PHP_VERSION, '8.3.0', '>=');
        if (! $is83) {
            return;
        }

        yield ReadonlyClass::class => [
            ReadonlyClass::class,
            realpath(__DIR__.'/Stubs/Decorated/readonly-class.php'),
        ];
    }

    /**
     * @param  class-string  $class
     * @param  class-string<Throwable>|null  $throwable
     *
     * @throws BindingResolutionException
     * @throws ReflectionException
     */
    #[DataProvider('get_instance_with_not_decoratable_class_dataProvider')]
    public function test_get_instance_with_not_decoratable_class(string $class, ?string $throwable): void
    {
        if ($throwable !== null) {
            $this->expectException($throwable);
        }
        $object = $this->factory->getInstance($class);

        $this->assertInstanceOf($class, $object);
        $this->assertFalse((new ReflectionClass($object))->isAnonymous());
    }

    public static function get_instance_with_not_decoratable_class_dataProvider(): Generator
    {
        yield 'Abstract' => [AbstractClass::class, BindingResolutionException::class];
        yield 'Final' => [FinalClass::class, null];
        yield 'Interface' => [InterfaceClass::class, BindingResolutionException::class];
        yield 'Trait' => [TraitClass::class, BindingResolutionException::class];

        $is83 = version_compare(PHP_VERSION, '8.3.0', '>=');
        if ($is83) {
            return;
        }

        yield 'Readonly' => [ReadonlyClass::class, null];
    }
}
