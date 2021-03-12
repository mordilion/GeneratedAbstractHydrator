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

namespace Mordilion\GeneratedAbstractHydrator\CodeGenerator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionProperty;
use function array_merge;
use function implode;
use function var_export;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class AbstractHydratorMethodsVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $visiblePropertyMap = [];

    /**
     * @var string[][]
     */
    private $hiddenPropertyMap = [];

    public function __construct(ReflectionClass $reflectedClass)
    {
        foreach ($this->findAllInstanceProperties($reflectedClass) as $property) {
            $className = $property->getDeclaringClass()->getName();

            if ($property->isPrivate() || $property->isProtected()) {
                $this->hiddenPropertyMap[$className][] = $property->getName();

                continue;
            }

            $this->visiblePropertyMap[] = $property->getName();
        }
    }

    public function leaveNode(Node $node): ?Class_
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $node->stmts[] = new Property(Class_::MODIFIER_PRIVATE, [
            new PropertyProperty('hydrateCallbacks', new Array_()),
            new PropertyProperty('extractCallbacks', new Array_()),
        ]);

        $this->replaceConstructor($this->findOrCreateMethod($node, '__construct'));
        $this->replaceHydrate($this->findOrCreateMethod($node, 'hydrate'));
        $this->replaceExtract($this->findOrCreateMethod($node, 'extract'));

        return $node;
    }

    /**
     * @param ReflectionClass|null $class
     *
     * @return ReflectionProperty[]
     */
    private function findAllInstanceProperties(?ReflectionClass $class = null): array
    {
        if (!$class) {
            return [];
        }

        return array_values(array_merge(
            $this->findAllInstanceProperties($class->getParentClass() ?: null),
            array_values(array_filter(
                $class->getProperties(),
                static function (ReflectionProperty $property): bool {
                    return !$property->isStatic();
                }
            ))
        ));
    }

    /**
     * @param string[] $parts
     * @param string[] $propertyNames
     */
    private function appendHydrateClosureParts(array &$parts, array $propertyNames): void
    {
        foreach ($propertyNames as $propertyName) {
            $parts[] = "    if (isset(\$values['" . $propertyName . "']) || " .
                '$object->' . $propertyName . " !== null && \\array_key_exists('" . $propertyName . "', \$values)) {";
            $parts[] = '        $object->' . $propertyName . " = \$that->hydrateValue('" . $propertyName . "', \$values['" . $propertyName . "'], \$object);";
            $parts[] = '    }';
        }
    }

    /**
     * @param string[] $parts
     * @param string[] $propertyNames
     */
    private function appendExtractClosureParts(array &$parts, array $propertyNames): void
    {
        foreach ($propertyNames as $propertyName) {
            $parts[] = "    \$values['" . $propertyName . "'] = \$that->extractValue('" . $propertyName . "', \$object->" . $propertyName . ', $object);';
        }
    }

    private function replaceConstructor(ClassMethod $method): void
    {
        $method->params = [];
        $bodyParts = ['parent::__construct();'];

        foreach ($this->hiddenPropertyMap as $className => $propertyNames) {
            // Hydrate closures
            $bodyParts[] = '$this->hydrateCallbacks[] = \\Closure::bind(static function ($object, $values, $that) {';
            $this->appendHydrateClosureParts($bodyParts, $propertyNames);
            $bodyParts[] = '}, null, ' . var_export($className, true) . ');' . "\n";

            // Extract closures
            $bodyParts[] = '$this->extractCallbacks[] = \\Closure::bind(static function ($object, &$values, $that) {';
            $this->appendExtractClosureParts($bodyParts, $propertyNames);
            $bodyParts[] = '}, null, ' . var_export($className, true) . ');' . "\n";
        }

        $method->stmts = (new ParserFactory())
            ->create(ParserFactory::ONLY_PHP7)
            ->parse('<?php ' . implode("\n", $bodyParts));
    }

    private function replaceHydrate(ClassMethod $method): void
    {
        $method->params = [
            new Param(new Node\Expr\Variable('data'), null, 'array'),
            new Param(new Node\Expr\Variable('object')),
        ];

        $bodyParts = [];
        foreach ($this->visiblePropertyMap as $propertyName) {
            $bodyParts[] = "if (isset(\$data['" . $propertyName . "']) || " .
                '$object->' . $propertyName . " !== null && \\array_key_exists('" . $propertyName . "', \$data)) {";
            $bodyParts[] = '    $object->' . $propertyName . " = \$this->hydrateValue('" . $propertyName . "', \$data['" . $propertyName . "'], \$object);";
            $bodyParts[] = '}';
        }
        $index = 0;
        foreach ($this->hiddenPropertyMap as $className => $propertyNames) {
            $bodyParts[] = '$this->hydrateCallbacks[' . ($index++) . ']->__invoke($object, $data, $this);';
        }

        $bodyParts[] = 'return $object;';

        $method->stmts = (new ParserFactory())
            ->create(ParserFactory::ONLY_PHP7)
            ->parse('<?php ' . implode("\n", $bodyParts));
    }

    private function replaceExtract(ClassMethod $method): void
    {
        $method->params = [new Param(new Node\Expr\Variable('object'))];

        $bodyParts   = [];
        $bodyParts[] = '$ret = array();';
        foreach ($this->visiblePropertyMap as $propertyName) {
            $bodyParts[] = "\$ret['" . $propertyName . "'] = \$this->extractValue('" . $propertyName . "', \$object->" . $propertyName . ', $object);';
        }
        $index = 0;
        foreach ($this->hiddenPropertyMap as $className => $propertyNames) {
            $bodyParts[] = '$this->extractCallbacks[' . ($index++) . ']->__invoke($object, $ret, $this);';
        }

        $bodyParts[] = 'return $ret;';

        $method->stmts = (new ParserFactory())
            ->create(ParserFactory::ONLY_PHP7)
            ->parse('<?php ' . implode("\n", $bodyParts));
    }

    /**
     * Finds or creates a class method (and eventually attaches it to the class itself)
     *
     * @deprecated not needed if we move away from code replacement
     */
    private function findOrCreateMethod(Class_ $class, string $name): ClassMethod
    {
        $foundMethods = array_filter(
            $class->getMethods(),
            static function (ClassMethod $method) use ($name): bool {
                return $name === (string) $method->name;
            }
        );

        $method = reset($foundMethods);

        if (!$method) {
            $class->stmts[] = $method = new ClassMethod($name);
        }

        return $method;
    }
}
