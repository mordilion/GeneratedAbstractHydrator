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
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use ReflectionClass;
use Zend\Hydrator\AbstractHydrator;
use function explode;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class AbstractHydratorGenerator implements HydratorGenerator
{
    public function generate(ReflectionClass $originalClass): array
    {
        $ast = [new Class_($originalClass->getShortName())];

        $namespace = $originalClass->getNamespaceName();
        if ($namespace) {
            $ast = [new Namespace_(new Name(explode('\\', $namespace)), $ast)];
        }

        $implementor = new NodeTraverser();
        $implementor->addVisitor(new AbstractHydratorMethodsVisitor($originalClass));
        $implementor->addVisitor(new ClassExtensionVisitor($originalClass->getName(), AbstractHydrator::class));

        return $implementor->traverse($ast);
    }
}
