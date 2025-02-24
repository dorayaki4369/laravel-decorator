<?php

namespace Dorayaki4369\LaravelDecorator;

use Dorayaki4369\LaravelDecorator\Facades\Decorator as LaravelDecoratorFacade;
use Illuminate\Contracts\Container\BindingResolutionException;
use PhpParser\Builder;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeAbstract;
use PhpParser\PrettyPrinter;
use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use UnexpectedValueException;

readonly class InstanceFactory
{
    public function __construct(
        protected BuilderFactory $factory,
    ) {}

    /**
     * @template T
     *
     * @param  class-string<T>  $class
     *
     * @throws BindingResolutionException
     * @throws ReflectionException
     */
    public function getInstance(string $class): object
    {
        $ref = new ReflectionClass($class);

        if (! isDecoratableClass($ref)) {
            return app($class);
        }

        $code = $this->generateCode($class);

        return eval($code);
    }

    /**
     * @param  class-string  $class
     *
     * @throws ReflectionException
     */
    public function generateCode(string $class): string
    {
        $ref = new ReflectionClass($class);

        $stmts = $this->createStmts($ref);

        $printer = new PrettyPrinter\Standard;

        return $printer->prettyPrint($stmts);
    }

    /**
     * @return Node\Stmt[]
     */
    protected function createStmts(ReflectionClass $ref): array
    {
        $stmts = [];

        $constructorArgs = [];
        if (($ref->getConstructor()?->getNumberOfParameters() ?? 0) > 0) {
            $stmts = $this->createInjectionStmts($ref);

            $constructorArgs[] = new Node\Arg(
                new Node\Expr\Variable('args'),
                unpack: true,
            );
        }

        $stmts[] = new Node\Stmt\Return_(
            new Node\Expr\New_(
                $this->createClassNode($ref),
                $constructorArgs,
            ),
        );

        return $stmts;
    }

    /**
     * @return Node\Stmt[]
     */
    protected function createInjectionStmts(ReflectionClass $ref): array
    {
        $methodVar = new Node\Expr\Variable('method');

        $methodVarAssign = new Node\Stmt\Expression(
            new Node\Expr\Assign(
                $methodVar,
                new Node\Expr\MethodCall(
                    new Node\Expr\New_(new Node\Name\FullyQualified(ReflectionClass::class), [
                        new Node\Arg(
                            new Node\Expr\ClassConstFetch(new Node\Name\FullyQualified($ref->getName()), 'class'),
                        ),
                    ]),
                    'getConstructor',
                ),
            ),
        );

        $ifStmt = new Node\Stmt\If_(new Node\Expr\BinaryOp\Identical($methodVar, new Node\Expr\ConstFetch(new Node\Name('null'))), [
            'stmts' => [
                new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable('args'), new Node\Expr\Array_)),
            ],
        ]);

        $arrayMapExpr = new Node\Expr\FuncCall(new Node\Name('array_map'), [
            new Node\Arg(new Node\Expr\Closure([
                'params' => [new Node\Param(new Node\Expr\Variable('p'))],
                'stmts' => [
                    new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable('class'),
                        new Node\Expr\NullsafeMethodCall(new Node\Expr\MethodCall(new Node\Expr\Variable('p'), 'getType'), 'getName')
                    )),
                    new Node\Stmt\Return_(
                        new Node\Expr\Ternary(
                            new Node\Expr\Variable('class'),
                            new Node\Expr\FuncCall(new Node\Name('app'), [new Node\Arg(new Node\Expr\Variable('class'))]),
                            new Node\Expr\ConstFetch(new Node\Name('null'))
                        )
                    ),
                ],
            ])),
            new Node\Arg(new Node\Expr\MethodCall($methodVar, 'getParameters')),
        ]);

        $ifStmt->else = new Node\Stmt\Else_([
            new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable('args'), $arrayMapExpr)),
        ]);

        return [$methodVarAssign, $ifStmt];
    }

    protected function createClassNode(ReflectionClass $ref): Node\Stmt\Class_
    {
        $builder = $this->factory->class('TemporallyClassName')->extend(new Node\Name\FullyQualified($ref->getName()));

        if ($ref->isReadOnly()) {
            $builder->makeReadOnly();
        }

        $builder->addStmts($this->createDecoratedMethods($ref));

        $classNode = $builder->getNode();
        $classNode->name = null;

        return $classNode;
    }

    protected function createDecoratedMethods(ReflectionClass $ref): array
    {
        return array_map(
            /**
             * @throws ReflectionException
             */
            fn (ReflectionMethod $method) => $this->createDecoratedMethod($method),
            getDecoratedMethods($ref),
        );
    }

    /**
     * @throws ReflectionException
     */
    protected function createDecoratedMethod(ReflectionMethod $ref): Builder\Method
    {
        $builder = $this->factory->method($ref->getName());

        $params = $this->createMethodParamBuilders($ref->getParameters());
        $builder->addParams($params);

        switch ($ref->getModifiers()) {
            case ReflectionMethod::IS_PUBLIC:
                $builder->makePublic();
                break;
            case ReflectionMethod::IS_PROTECTED:
                $builder->makeProtected();
                break;
            case ReflectionMethod::IS_PRIVATE:
                $builder->makePrivate();
                break;
            default:
                break;
        }

        if (null !== $type = $ref->getReturnType()) {
            $builder->setReturnType($this->createTypeNode($type));
        }

        $builder->addStmt($this->createDecoratedMethodReturnStmt($ref));

        return $builder;
    }

    /**
     * @param  ReflectionParameter[]  $params
     * @return Node\Param[]
     *
     * @throws ReflectionException
     */
    protected function createMethodParamBuilders(array $params): array
    {
        return array_map(
            function (ReflectionParameter $ref) {
                $param = $this->factory->param($ref->getName());

                if (null !== $type = $ref->getType()) {
                    $param->setType($this->createTypeNode($type));
                }

                if ($ref->isDefaultValueAvailable()) {
                    $param->setDefault($ref->getDefaultValue());
                }

                if ($ref->isPassedByReference()) {
                    $param->makeByRef();
                }

                if ($ref->isVariadic()) {
                    $param->makeVariadic();
                }

                return $param;
            },
            $params,
        );
    }

    protected function createTypeNode(ReflectionType $ref): NodeAbstract
    {
        if ($ref instanceof ReflectionIntersectionType) {
            return $this->createCompositeType($ref);
        }

        if ($ref instanceof ReflectionUnionType) {
            return $this->createCompositeType($ref);
        }

        if ($ref instanceof ReflectionNamedType) {
            return $this->createNamedType($ref);
        }

        throw new UnexpectedValueException('Unexpected type');
    }

    protected function createNamedType(ReflectionNamedType $ref): Node\Identifier|Node\Name\FullyQualified
    {
        $name = $ref->getName();

        if ($ref->isBuiltin()) {
            if ($name !== 'mixed') {
                $name = $ref->allowsNull() ? '?'.$name : $name;
            }

            return new Node\Identifier($name);
        }

        return new Node\Name\FullyQualified($name);
    }

    protected function createCompositeType(ReflectionIntersectionType|ReflectionUnionType $ref): Node\IntersectionType|Node\UnionType
    {
        $types = array_map(fn (ReflectionType $t) => $this->createTypeNode($t), $ref->getTypes());

        if ($ref instanceof ReflectionIntersectionType) {
            return new Node\IntersectionType($types);
        }

        return new Node\UnionType($types);
    }

    protected function createDecoratedMethodReturnStmt(ReflectionMethod $ref): Node\Stmt\Return_
    {
        $args = [
            new Node\Arg(new Node\Expr\Variable('this')),
            new Node\Arg(new Node\Scalar\MagicConst\Function_),
        ];

        $parameters = $ref->getParameters();
        if (count($parameters) > 0) {
            $values = [];
            foreach ($parameters as $p) {
                if ($p->isVariadic()) {
                    $values[] = new Node\Arg(new Node\Expr\Variable($p->getName()), false, true);
                } else {
                    $values[] = new Node\Arg(
                        new Node\Expr\Variable($p->getName()),
                        $p->isPassedByReference(),
                    );
                }
            }

            $args[] = new Node\Arg(new Node\Expr\Array_($values));
        } else {
            $args[] = new Node\Arg(new Node\Expr\Array_);
        }

        return new Node\Stmt\Return_(
            new Node\Expr\StaticCall(
                new Node\Name\FullyQualified(LaravelDecoratorFacade::class),
                'handle',
                $args,
            )
        );
    }
}
