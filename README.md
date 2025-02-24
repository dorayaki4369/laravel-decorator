# Laravel Decorator

This package provides a simple way to implement the decorator pattern in Laravel.

## Installation

Require this package with composer using the following command:

```bash
composer require dorayaki4369/laravel-decorator
```

## Usage

### 1. Create a decorator

You can create a decorator by implementing the `Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator` interface.

The decorator class must implement the `decorate` method.
the method is a similar to `handle` method of [Middleware](https://laravel.com/docs/11.x/middleware).

```php
namespace App\Attributes;

use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;

class LogDecorator extends Decorator
{
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed
    {
        // Before the method is called
        \Illuminate\Support\Facades\Log::debug('Before the method is called');
        
        $result = $next($args, $instance, $parentClass, $method);
        
        // After the method is called
        \Illuminate\Support\Facades\Log::debug('After the method is called');
        
        return $result;
    }
}
```

### 2. Apply the decorator

You can apply the decorator to a method.
The applicable classes and methods must meet the following conditions:

1. The target class must be instantiable from the service container.
2. The target class or method must not be final.
3. The target method must be public.
4. The target method must not be a static method.
5. When you using php 8.3 or earlier, the target class must not be a readonly class.

```php
namespace App\Services;

use App\Attributes\LogDecorator;
use Illuminate\Contracts\Foundation\Application;

class MyService
{
    public function __constructor(
        // You can define the constructor but the constructor's arguments must be able to resolve from the service container.
        public readonly Application $app,
    )
    {   
    }

    #[LogDecorator]
    public function handle(int $value1, int $value2): int
    {
        return $value1 + $value2;
    }
}
```

You can apply multiple decorators to a method.
The applied decorators are executed in the order of the attributes.

```php
namespace App\Services;

use App\Attributes\LogDecorator;
use App\Attributes\CacheDecorator;

class MyService
{
    public function __constructor(
        // You can define the constructor but the constructor's arguments must be able to resolve from the service container.
        public readonly Application $app,
    )
    {   
    }

    #[LogDecorator]
    #[CacheDecorator] // the CacheDecorator will be executed after the LogDecorator
    public function handle(int $value1, int $value2): int
    {
        return $value1 + $value2;
    }
}
```

### 3. Call the method

You can call the method as usual.

```php
namespace App\Http\Controllers;

use App\Services\MyService;

class HomeController
{
    public function index(MyService $service): int
    {
        return $service->handle(1, 2); // When this line is executed, the LogDecorator and CacheDecorator decorators are executed in order before and after the function.
    }
}
```

## Default decorators

This package already implements some commonly used decorators.

### `DBTransactionDecorator`

This decorator wraps the method in a database transaction.
If an exception is thrown, the transaction is rolled back.

```php
namespace App\Services;

use Dorayaki4369\LaravelDecorator\Attributes\DBTransactionDecorator;
use App\Models\User;

class UserHandler
{
    #[DBTransactionDecorator]
    public function handle(array $attributes): User
    {
        User::create($attributes);
    }
}
```

### `SimpleCacheDecorator`

This decorator caches the result of the method.

At execution time, a cache key is created from the class name, method name, and arguments, and the execution results are cached.
If the same condition is executed from the second time onwards, the cached results will be returned.

```php
namespace App\Services;

use Dorayaki4369\LaravelDecorator\Attributes\SimpleCacheDecorator;
use App\Models\User;

class UserHandler
{
    #[SimpleCacheDecorator]
    public function handle(string $name): User
    {
        return User::where('name', $name)->first();
    }
}
```

### `ValidationDecorator`

This decorator validates the arguments of the method by [Validation](https://laravel.com/docs/11.x/validation);

```php
namespace App\Services;

use Dorayaki4369\LaravelDecorator\Attributes\ValidationDecorator;
use App\Models\User;

class UserHandler
{
    #[ValidationDecorator([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
    ])]
    public function handle(array $attributes): User
    {
        return User::create($attributes);
    }
}
```

## How it works in the background

To provide the easiest and simplest decorator functionality, this package generates an anonymous class that overrides the decorated method when the target class is instantiated from the service container.
The reason there are some conditions on the class that can be decorated is because this anonymous class inherits from the target class.
Read-only anonymous classes are a PHP 8.3 and later feature, so they will not work in earlier versions of PHP.

For example, the `MyService` class resolving from the service container, the package generates the following anonymous class.

```php
$method = (new \ReflectionClass(\Dorayaki4369\LaravelDecorator\Tests\Stubs\Targets\InjectionRequiredClass::class))->getConstructor();
if ($method === null) {
    $args = [];
} else {
    $args = array_map(function ($p) {
        $class = $p->getType()?->getName();

        return $class ? app($class) : null;
    }, $method->getParameters());
}

return new class(...$args) extends \App\Services\MyService {
    public function handle(int $value1, int $value2): int
    {   
        return \Dorayaki4369\LaravelDecorator\Facades\Decorator::handle($this, __FUNCTION__, [$value1, $value2]);
    }
}
```

If you want to more specification of decorated class, You can find out in [the Package's test codes](https://github.com/dorayaki4369/decoravel/tree/main/tests).

## License

The Laravel Decorator is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
