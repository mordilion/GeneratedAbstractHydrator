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

namespace Mordilion\GeneratedAbstractHydrator\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionProperty;
use Zend\Hydrator\HydratorInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait AnnotationHydrationTrait
{
    /**
     * @var AnnotationReader|null
     */
    private $annotationReader;

    /**
     * @var object[][][]
     */
    private $propertyAnnotations;

    /**
     * @var ReflectionClass|null
     */
    private $reflectionClass;

    public function load($cache): bool
    {

    }

    public function modifyHydrator(HydratorInterface $hydrator): HydratorInterface
    {
        $annotations = $this->getAnnotations();
    }

    private function getAnnotation(ReflectionProperty $property, string $class): array
    {
        $result = [];
        $annotations = $this->annotationReader->getPropertyAnnotations($property);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $class) {
                $result[] = $annotation;
            }
        }

        return $result;
    }

    private function getAnnotations(string $propertyName, array $classes): array
    {
        $annotations = [];

        foreach ($classes as $class) {
            if (isset($this->propertyAnnotations[$propertyName][$class])) {
                $annotations[] = $this->propertyAnnotations[$propertyName][$class];
            }

            $annotations[] = $this->getAnnotation($this->getReflectionProperty($propertyName), $class);
        }

        return array_merge(...$annotations);
    }

    private function getAnnotationReader(): AnnotationReader
    {
        if ($this->annotationReader) {
            return $this->annotationReader;
        }

        $this->annotationReader = new AnnotationReader();

        return $this->annotationReader;
    }

    private function getReflectionProperty(string $name): ReflectionProperty
    {
        $reflectionClass = $this->reflectionClass ?: new ReflectionClass($this);

        return $reflectionClass->getProperty($name);
    }
}
