<?php

namespace Dorayaki4369\Decoravel;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use ReflectionClass;
use ReflectionException;

class DecorateGenerator
{
    /**
     * @param class-string $class
     * @return object
     * @throws ReflectionException
     */
    public function decorate(string $class): object {
        return eval($this->generateCode($class));
    }

    /**
     * @param class-string $class
     * @return string
     * @throws ReflectionException
     */
    protected function generateCode(string $class): string {
        $ref = new ReflectionClass($class);

    }

    protected function createAST(ReflectionClass $ref): string {
        $factory = new BuilderFactory();

        $node = $factory->namespace(null)
            ->addStmt($factory->use(Return_::class));
    }
}