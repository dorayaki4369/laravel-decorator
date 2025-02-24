<?php

namespace Dorayaki4369\LaravelDecorator\Attributes;

use Attribute;
use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\Cache;

#[Attribute(Attribute::TARGET_METHOD)]
class SimpleCacheDecorator implements Decorator
{
    /**
     * Wrap the decorated method with a simple caching mechanism.
     *
     * @param callable $next
     * @param array $args
     * @param object $instance
     * @param string $parentClass
     * @param string $method
     * @return mixed
     */
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed
    {
        $cacheKey = $this->getCacheKey($args, $parentClass, $method);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $result = $next($args, $instance, $parentClass, $method);

        Cache::put($cacheKey, $result);

        return $result;
    }

    protected function getCacheKey(array $args, string $parentClass, string $method): string
    {
        return $parentClass.'::'.$method.'::'.md5(serialize($args));
    }
}
