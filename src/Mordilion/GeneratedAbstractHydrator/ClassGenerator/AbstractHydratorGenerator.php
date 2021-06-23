<?php

/**
 * This file is part of the GeneratedAbstractHydrator package.
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 *
 * @copyright (c) Henning Huncke - <mordilion@gmx.de>
 */

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\ClassGenerator;

use CodeGenerationUtils\Visitor\ClassExtensionVisitor;
use Mordilion\GeneratedAbstractHydrator\CodeGenerator\Visitor\AbstractHydratorMethodsVisitor;
use GeneratedHydrator\ClassGenerator\HydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use ReflectionClass;
use Zend\Hydrator\HydratorInterface;
use function explode;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class AbstractHydratorGenerator implements HydratorGenerator
{
    /**
     * @var class-string
     */
    private $abstractClass;

    /**
     * @param class-string $abstractClass
     */
    public function __construct(string $abstractClass)
    {
        $reflection = new ReflectionClass($abstractClass);

        if (!$reflection->isSubclassOf(HydratorInterface::class)) {
            throw new InvalidArgumentException(sprintf('The provided $abstractClass must be a sub class of %s', HydratorInterface::class));
        }

        if (!$reflection->isAbstract()) {
            throw new InvalidArgumentException('The provided $abstractClass must be an abstract class');
        }

        $this->abstractClass = $abstractClass;
    }

    public function generate(ReflectionClass $originalClass): array
    {
        $ast = [new Class_($originalClass->getShortName())];
        $namespace = $originalClass->getNamespaceName();

        if ($namespace) {
            $ast = [new Namespace_(new Name(explode('\\', $namespace)), $ast)];
        }

        $implementor = new NodeTraverser();
        $implementor->addVisitor(new AbstractHydratorMethodsVisitor($originalClass));
        $implementor->addVisitor(new ClassExtensionVisitor($originalClass->getName(), $this->abstractClass));

        return $implementor->traverse($ast);
    }
}
