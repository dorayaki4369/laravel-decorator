<?php

namespace Dorayaki4369\LaravelDecorator\Attributes;

use Attribute;
use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Attribute(Attribute::TARGET_METHOD)]
class DBTransactionDecorator implements Decorator
{
    /**
     * Wrap the decorated method with a database transaction.
     *
     * @param  callable(array $args, object $instance, string $parentClass, string $method): mixed  $next
     *
     * @throws Throwable
     */
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed
    {
        try {
            DB::beginTransaction();

            $result = $next($args, $instance, $parentClass, $method);

            DB::commit();

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
