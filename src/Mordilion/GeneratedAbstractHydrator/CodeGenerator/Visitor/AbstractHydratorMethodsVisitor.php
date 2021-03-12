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
use Mordilion\GeneratedAbstractHydrator\Annotation\Type;
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
use Zend\Hydrator\NamingStrategy\ArrayMapNamingStrategy;
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
     * @param ReflectionProperty[] $propertyNames
     */
    private function appendHydrateClosureParts(array &$parts, array $properties): void
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyTypecast = $this->getPropertyTypecast($property);

            $parts[] = "    \$name = \$that->extractName('" . $propertyName . "', \$object);";
            $parts[] = "    if (isset(\$values[\$name]) || " .
                '$object->' . $propertyName . " !== null && \\array_key_exists(\$name, \$values)) {";
            $parts[] = '        $object->' . $propertyName . " = " . $propertyTypecast . "\$that->hydrateValue('" . $propertyName . "', \$values[\$name], \$object);";
            $parts[] = '    }';
        }
    }

    /**
     * @param string[] $parts
     * @param ReflectionProperty[] $propertyNames
     */
    private function appendExtractClosureParts(array &$parts, array $properties): void
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyTypecast = $this->getPropertyTypecast($property);

            $parts[] = "    \$name = \$that->extractName('" . $propertyName . "', \$object);";
            $parts[] = "    \$values[\$name] = " . $propertyTypecast . "\$that->extractValue('" . $propertyName . "', \$object->" . $propertyName . ', $object);';
        }
    }

    private function replaceConstructor(ClassMethod $method): void
    {
        $method->params = [];
        $bodyParts = ['parent::__construct();'];

        foreach ($this->hiddenPropertyMap as $className => $properties) {
            // Add ArrayMapNamingStrategy for SerializedName-Annotations
            $mapping = array_map(static function ($value, $key) {
                return sprintf("'%s' => '%s'", $key, $value);
            }, $this->nameMapping, array_keys($this->nameMapping));
            $bodyParts[] = '$this->setNamingStrategy(new \\' . ArrayMapNamingStrategy::class . '([' . implode(',', $mapping) . ']));';

            // Hydrate closures
            $bodyParts[] = '$this->hydrateCallbacks[] = \\Closure::bind(static function ($object, $values, $that) {';
            $this->appendHydrateClosureParts($bodyParts, $properties);
            $bodyParts[] = '}, null, ' . var_export($className, true) . ');' . "\n";

            // Extract closures
            $bodyParts[] = '$this->extractCallbacks[] = \\Closure::bind(static function ($object, &$values, $that) {';
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
            $propertyTypecast = $this->getPropertyTypecast($property);

            $bodyParts[] = "\$name = \$this->extractName('" . $propertyName . "', \$object);";
            $bodyParts[] = "if (isset(\$data[\$name]) || " .
                '$object->' . $propertyName . " !== null && \\array_key_exists(\$name, \$data)) {";
            $bodyParts[] = '    $object->' . $propertyName . " = " . $propertyTypecast . "\$this->hydrateValue('" . $propertyName . "', \$data[\$name], \$object);";
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
        $bodyParts[] = '$ret = [];';
        foreach ($this->visiblePropertyMap as $property) {
            $propertyName = $property->getName();
            $propertyTypecast = $this->getPropertyTypecast($property);

            $bodyParts[] = "\$name = \$this->extractName('" . $propertyName . "', \$object);";
            $bodyParts[] = "\$ret[\$name] = " . $propertyTypecast . "\$this->extractValue('" . $propertyName . "', \$object->" . $propertyName . ', $object);';
        }

        $index = 0;
        foreach ($this->hiddenPropertyMap as $className => $properties) {
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

    private function getAnnotation(ReflectionProperty $property, string $class): ?object
    {
        $annotations = $this->annotationReader->getPropertyAnnotations($property);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $class) {
                return $annotation;
            }
        }

        return null;
    }

    private function getPropertyTypecast(ReflectionProperty $property): string
    {
        /** @var Type|null $annotation */
        $annotation = $this->getAnnotation($property, Type::class);

        if ($annotation === null || $annotation->getType() === Type::TYPE_UNKNOWN) {
            return '';
        }

        return $annotation->getTypecast() . " ";
    }

    private function getSerializedPropertyName(ReflectionProperty $property): string
    {
        /** @var SerializedName|null $annotation */
        $annotation = $this->getAnnotation($property, SerializedName::class);

        if ($annotation === null) {
            return $property->getName();
        }

        return $annotation->getName();
    }
}
