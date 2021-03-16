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

use Doctrine\Common\Annotations\AnnotationReader;
use Mordilion\GeneratedAbstractHydrator\Annotation\SerializedName;
use Mordilion\GeneratedAbstractHydrator\Annotation\Strategy;
use Mordilion\GeneratedAbstractHydrator\Annotation\Type;
use Mordilion\GeneratedAbstractHydrator\Annotation\StrategyBuilder;
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
use Zend\Hydrator\AbstractHydrator;
use Zend\Hydrator\NamingStrategy\ArrayMapNamingStrategy;
use Zend\Hydrator\Strategy\StrategyChain;
use function array_merge;
use function implode;
use function var_export;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class AbstractHydratorMethodsVisitor extends NodeVisitorAbstract
{
    /**
     * @var ReflectionProperty[]
     */
    private $allProperties = [];

    /**
     * @var ReflectionProperty[]
     */
    private $visiblePropertyMap = [];

    /**
     * @var ReflectionProperty[][]
     */
    private $hiddenPropertyMap = [];

    /**
     * @var string[]
     */
    private $nameMapping = [];

    /**
     * @var object[][][]
     */
    private $propertyAnnotations = [];

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(ReflectionClass $reflectedClass)
    {
        $this->annotationReader = new AnnotationReader();

        foreach ($this->findAllInstanceProperties($reflectedClass) as $property) {
            $className = $property->getDeclaringClass()->getName();
            $serializedPropertyName = $this->getSerializedPropertyName($property);

            if ($serializedPropertyName !== $property->getName()) {
                $this->nameMapping[$property->getName()] = $serializedPropertyName;
            }

            $this->allProperties[] = $property;

            if ($property->isPrivate() || $property->isProtected()) {
                $this->hiddenPropertyMap[$className][] = $property;

                continue;
            }

            $this->visiblePropertyMap[] = $property;
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
     * @param ReflectionProperty[] $properties
     */
    private function appendStrategies(array &$parts, array $properties): void
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            /** @var Type[] $typeAnnotations */
            $typeAnnotations = $this->getAnnotations($property, Type::class);
            /** @var Strategy[] $typeAnnotations */
            $strategyAnnotations = $this->getAnnotations($property, Strategy::class);

            if (empty($typeAnnotations) && empty($strategyAnnotations)) {
                continue;
            }

            $parts[] = '$this->addStrategy(\'' . $propertyName . '\', new \\' . StrategyChain::class . '([';

            foreach (array_merge($typeAnnotations, $strategyAnnotations) as $annotation) {
                $buildParts = array_map(static function (string $item) {
                    return '    ' . $item . ',';
                }, StrategyBuilder::build($property, $annotation));
                $parts = array_merge(array_values($parts), array_values($buildParts));
            }

            $parts[] = ']));';
        }
    }

    /**
     * @param string[] $parts
     * @param ReflectionProperty[] $properties
     */
    private function appendHydrateClosureParts(array &$parts, array $properties): void
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $parts[] = "    \$name = \$that->extractName('" . $propertyName . "', \$object);";
            $parts[] = "    if (isset(\$data[\$name]) || " .
                '$object->' . $propertyName . " !== null && \\array_key_exists(\$name, \$data)) {";
            $parts[] = $this->getPropertyHydrateString($property, 2, true);
            $parts[] = '    }';
        }
    }

    /**
     * @param string[] $parts
     * @param ReflectionProperty[] $properties
     */
    private function appendExtractClosureParts(array &$parts, array $properties): void
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $parts[] = "    \$name = \$that->extractName('" . $propertyName . "', \$object);";
            $parts[] = $this->getPropertyExtractString($property, 1, true);
        }
    }

    private function replaceConstructor(ClassMethod $method): void
    {
        $method->params = [];
        $bodyParts = ['parent::__construct();', ''];

        // Add ArrayMapNamingStrategy for SerializedName-Annotations
        $mapping = array_map(static function ($value, $key) {
            return sprintf("'%s' => '%s'", $key, $value);
        }, $this->nameMapping, array_keys($this->nameMapping));
        $bodyParts[] = '$this->setNamingStrategy(new \\' . ArrayMapNamingStrategy::class . '([' . implode(',', $mapping) . ']));';
        $this->appendStrategies($bodyParts, $this->allProperties);

        foreach ($this->hiddenPropertyMap as $className => $properties) {
            // Hydrate closures
            $bodyParts[] = '$this->hydrateCallbacks[] = \\Closure::bind(static function ($object, $data, $that) {';
            $this->appendHydrateClosureParts($bodyParts, $properties);
            $bodyParts[] = '}, null, ' . var_export($className, true) . ');' . "\n";

            // Extract closures
            $bodyParts[] = '$this->extractCallbacks[] = \\Closure::bind(static function ($object, &$data, $that) {';
            $this->appendExtractClosureParts($bodyParts, $properties);
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
        foreach ($this->visiblePropertyMap as $property) {
            $propertyName = $property->getName();

            $bodyParts[] = "\$name = \$this->extractName('" . $propertyName . "', \$object);";
            $bodyParts[] = "if (isset(\$data[\$name]) || " .
                '$object->' . $propertyName . " !== null && \\array_key_exists(\$name, \$data)) {";
            $bodyParts[] = $this->getPropertyHydrateString($property, 1);
            $bodyParts[] = '}';
        }

        $index = 0;
        foreach ($this->hiddenPropertyMap as $className => $properties) {
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
        $bodyParts[] = '$data = [];';
        foreach ($this->visiblePropertyMap as $property) {
            $propertyName = $property->getName();

            $bodyParts[] = "\$name = \$this->extractName('" . $propertyName . "', \$object);";
            $bodyParts[] = $this->getPropertyExtractString($property, 1);
        }

        $index = 0;
        foreach ($this->hiddenPropertyMap as $className => $properties) {
            $bodyParts[] = '$this->extractCallbacks[' . ($index++) . ']->__invoke($object, $data, $this);';
        }

        $bodyParts[] = 'return $data;';

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

    /**
     * @return object[]
     */
    private function getAnnotations(ReflectionProperty $property, string $class): array
    {
        if (!isset($this->propertyAnnotations[$property->getName()][$class])) {
            $annotations = $this->annotationReader->getPropertyAnnotations($property);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof $class) {
                    $this->propertyAnnotations[$property->getName()][$class][] = $annotation;
                }
            }
        }

        return $this->propertyAnnotations[$property->getName()][$class] ?? [];
    }

    private function getSerializedPropertyName(ReflectionProperty $property): string
    {
        /** @var SerializedName[] $annotations */
        $annotations = $this->getAnnotations($property, SerializedName::class);

        if (empty($annotations)) {
            return $property->getName();
        }

        $annotation = reset($annotations);

        return $annotation->getName();
    }

    private function getPropertyHydrateString(ReflectionProperty $property, int $indent, bool $isThat = false)
    {
        $propertyName = $property->getName();

        $result = str_repeat('    ', $indent);
        $result .= '$object->' . $propertyName . " = ";
        $value = ($isThat ? '$that' : '$this') . "->hydrateValue('" . $propertyName . "', \$data[\$name], \$object)";
        $result .= $value . ";";

        return $result;
    }

    private function getPropertyExtractString(ReflectionProperty $property, int $indent, bool $isThat = false): string
    {
        $propertyName = $property->getName();

        $result = str_repeat('    ', $indent);
        $result .= "\$data[\$name] = ";
        $value = ($isThat ? '$that' : '$this') . "->extractValue('" . $propertyName . "', \$object->" . $propertyName . ", \$object)";
        $result .= $value . ";";

        return $result;
    }
}
