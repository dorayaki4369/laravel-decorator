<?php

namespace Dorayaki4369\LaravelDecorator\Attributes;

use Attribute;
use Dorayaki4369\LaravelDecorator\Contracts\Attributes\Decorator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

#[Attribute(Attribute::TARGET_METHOD)]
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
