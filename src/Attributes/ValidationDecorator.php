<?php

namespace Dorayaki4369\Decoravel\Attributes;

use Dorayaki4369\Decoravel\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationDecorator implements Decorator
{
    public function __construct(
        protected array $rules,
        protected array $messages = [],
        protected array $attributes = [],
    ) {}

    /**
     * Wrap the decorated method with a validator.
     *
     * @param callable $next
     * @param array $args
     * @param object $instance
     * @param string $parentClass
     * @param string $method
     * @return mixed
     * @throws ValidationException
     */
    public function decorate(callable $next, array $args, object $instance, string $parentClass, string $method): mixed
    {
        $validator = Validator::make($args, $this->rules, $this->messages, $this->attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $next($validator->validated(), $instance, $parentClass, $method);
    }
}
