# Laravel Decorator

The helper functions for decorator pattern in Laravel.

This package provides a simple way to implement the decorator pattern in Laravel.

## Installation

Please install the package via composer.

```bash
composer require dorayaki4369/laravel-decorator
```

After installing, your Laravel application will be load the `Dorayaki4369\\LaravelDecorator\\LaravelDecoratorServiceProvider` automatically.

## Usage

### 1. Create a decorator

You can create a decorator by extending the `Dorayaki4369\LaravelDecorator\Decorator` class.

The decorator class is a similar to [middleware](https://laravel.com/docs/11.x/middleware).
It has a `decorate` method that receives the arguments, the instance, the method name, and the next callable.

```php

namespace App\Attributes;

use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;

class LogDecorator extends Decorator
{
    public function decorate(callable $next, array $args, object $instance, string $method): mixed
    {
        // Before the method is called
        \Illuminate\Support\Facades\Log::debug('Before the method is called');
        
        $result = $next($args);
        
        // After the method is called
        \Illuminate\Support\Facades\Log::debug('After the method is called');
        
        return $result;
    }
}
```

### 2. Apply the decorator

You can apply the decorator to a method.
The target class must not be a final class and when you using php 8.1 or earlier, the class must not be a readonly class.

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

If you want to multiple decorators, decorate the method with multiple attributes.
The applied decorators are executed in the order of the attributes.

```php
namespace App\Services;

use App\Attributes\LogDecorator;
use App\Attributes\CacheDecorator;

class MyService
{
    #[LogDecorator]
    #[CacheDecorator] // the CacheDecorator will be executed after the LogDecorator
    public function handle(int $value1, int $value2): int
    {
        return $value1 + $value2;
    }
}
```

### 3. Call the method

You can call the method as usual, but the decorated class must be resolved from the service container.

```php
namespace App\Http\Controllers;

use App\Services\MyService;

class HomeController
{
    public function index(MyService $service): int
    {
        return $service->handle(1, 2); // When this line is executed, the log will be output.
    }
}
```

## How it works in the background

This package generates an anonymous class that extends a target class and overrides the decorated methods when the target class resolving from the service container.
The reason why it is not possible to decorate against a final class is due to the specification that this anonymous class inherits from the target class.
Also, readonly anonymous classes are a feature of PHP 8.2 or later, so earlier versions of PHP will not work.

For example, the `MyService` class resolving from the service container, the package generates the following anonymous class.

```php
return new class extends \App\Services\MyService {
    public function __construct(
        \Illuminate\Contracts\Foundation\Application $app
    ) {
        parent::__construct($app);
    }
    
    public function handle(int $value1, int $value2): int
    {   
        return \Dorayaki4369\LaravelDecorator\Facades\Decorator::handle($this, 'handle', $value1, $value2);
    }
}
```
