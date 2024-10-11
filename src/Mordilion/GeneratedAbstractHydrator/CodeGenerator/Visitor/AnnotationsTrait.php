<?php

/**
 * This file is part of the GeneratedAbstractHydrator package.
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 * @copyright (c) Henning Huncke - <mordilion@gmx.de>
 */

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\CodeGenerator\Visitor;

use Doctrine\Common\Annotations\AnnotationReader;
use Laminas\Hydrator\HydratorInterface;
use Mordilion\GeneratedAbstractHydrator\CodeGenerator\Annotations\Strategy;
use Mordilion\GeneratedAbstractHydrator\CodeGenerator\Annotations\Type;
use ReflectionClass;
use ReflectionProperty;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait AnnotationsTrait
{
    private ?AnnotationReader $annotationReader = null;

    /**
     * @var object[][][]
     */
    private array $propertyAnnotations = [];

    private ?ReflectionClass $reflectionClass = null;

    public function generateCode(string $class): string
    {
        $code = '';
        $this->reflectionClass = $this->reflectionClass ?: new ReflectionClass($class);

        foreach ($this->reflectionClass->getProperties() as $reflectionProperty) {
            $annotations = $this->getAnnotations($reflectionProperty, [Type::class, Strategy::class]);

            /** @var Type $annotation */
            foreach ($annotations[Type::class] ?? [] as $annotation) {
                $code .= '$this->addStrategy(\'' . $reflectionProperty->getName() . '\', ' . $annotation->generateCode(). ');' . PHP_EOL;
            }

            /** @var Strategy $annotation */
            foreach ($annotations[Strategy::class] ?? [] as $annotation) {
                $code .= '$this->addStrategy(\'' . $reflectionProperty->getName() . '\', ' . $annotation->generateCode(). ');' . PHP_EOL;
            }
        }

        return $code;
    }

    private function getAnnotation(ReflectionProperty $reflectionProperty, string $class): array
    {
        $result = [];
        $annotations = $this->getAnnotationReader()->getPropertyAnnotations($reflectionProperty);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $class) {
                $result[] = $annotation;
            }
        }

        return $result;
    }

    private function getAnnotationReader(): AnnotationReader
    {
        if ($this->annotationReader) {
            return $this->annotationReader;
        }

        $this->annotationReader = new AnnotationReader();

        return $this->annotationReader;
    }

    private function getAnnotations(ReflectionProperty $reflectionProperty, array $classes): array
    {
        $annotations = [];
        $reflectionPropertyName = $reflectionProperty->getName();

        foreach ($classes as $class) {
            if (isset($this->propertyAnnotations[$reflectionPropertyName][$class])) {
                $annotations[] = $this->propertyAnnotations[$reflectionPropertyName][$class];
            }

            $annotations[] = $this->getAnnotation($reflectionProperty, $class);
        }

        return array_merge(...$annotations);
    }
}
