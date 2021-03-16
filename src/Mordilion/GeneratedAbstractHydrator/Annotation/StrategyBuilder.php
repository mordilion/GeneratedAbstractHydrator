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

namespace Mordilion\GeneratedAbstractHydrator\Annotation;

use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Exception\RuntimeException;
use Mordilion\GeneratedAbstractHydrator\Strategy\BooleanStrategy;
use Mordilion\GeneratedAbstractHydrator\Strategy\FloatStrategy;
use Mordilion\GeneratedAbstractHydrator\Strategy\IntegerStrategy;
use Mordilion\GeneratedAbstractHydrator\Strategy\RecursiveHydrationStrategy;
use Mordilion\GeneratedAbstractHydrator\Strategy\StringStrategy;
use ReflectionProperty;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class StrategyBuilder
{
    public static function build(ReflectionProperty $property, ParserAwareInterface $annotation): array
    {
        if ($annotation instanceof Type) {
            return self::buildForType($property->getName(), [$annotation->getParameters()]);
        }

        if ($annotation instanceof Strategy) {
            return self::buildForStrategy($property->getName(), [$annotation->getParameters()]);
        }

        return [];
    }

    public static function buildForType(string $propertyName, array $parameters, $isCollection = false): array
    {
        $parts = [];

        foreach ($parameters as $parameter) {
            if ($parameter['type'] === 'simple') {
                $parts = self::handleSimple($parameter, $parts);
            }

            if ($parameter['type'] === 'object') {
                $parts = self::handleObject($propertyName, $parameter, $isCollection, $parts, 'type');
            }

            if ($parameter['type'] === 'complex') {
                $parts = array_merge(
                    $parts,
                    self::buildForType($propertyName, [$parameter['data']] ?? []),
                    self::buildForType($propertyName, $parameter['params'] ?? [], true)
                );
            }
        }

        return $parts;
    }

    public static function buildForStrategy(string $propertyName, array $parameters, $isCollection = false): array
    {
        $parts = [];

        foreach ($parameters as $parameter) {
            if ($parameter['type'] === 'simple') {
                $parts = self::handleSimple($parameter, $parts);
            }

            if ($parameter['type'] === 'object') {
                $parts = self::handleObject($propertyName, $parameter, $isCollection, $parts, 'strategy');
            }

            if ($parameter['type'] === 'complex') {
                $parts = array_merge(
                    $parts,
                    self::buildForStrategy($propertyName, [$parameter['data']] ?? []),
                    self::buildForStrategy($propertyName, $parameter['params'] ?? [], true)
                );
            }
        }

        return $parts;
    }

    private static function getStrategyName(string $name): string
    {
        if ($name === 'string') {
            return '\\' . StringStrategy::class;
        }

        if (in_array($name, ['int', 'integer'], true)) {
            return '\\' . IntegerStrategy::class;
        }

        if (in_array($name, ['bool', 'boolean'], true)) {
            return '\\' . BooleanStrategy::class;
        }

        if (in_array($name, ['double', 'float'], true)) {
            return '\\' . FloatStrategy::class;
        }

        return '';
    }

    private static function getClassHydrator(string $class): string
    {
        $class = ltrim($class, '\\');

        $config = new Configuration($class);
        $config->setHydratorGenerator(new AbstractHydratorGenerator());
        $hydratorClass = $config->createFactory()->getHydratorClass();

        if (!class_exists($hydratorClass)) {
            throw new RuntimeException(sprintf('Hydrator-Class for "%s" does not exist', $class));
        }

        return $hydratorClass;
    }

    private static function handleSimple(array $parameter, array $parts): array
    {
        $strategyName = self::getStrategyName($parameter['name'] ?? '');
        $value = $parameter['value'] ?? null;

        if ($value === null && !empty($strategyName)) {
            $parts[] = 'new ' . $strategyName . '()';
        }

        if ($value !== null) {
            $escapeChar = $parameter['quote'] ?? '';
            $value = str_replace($escapeChar, $escapeChar . $escapeChar, $value);
            $parts[] = $escapeChar . $value . $escapeChar;
        }

        return $parts;
    }

    private static function handleObject(string $propertyName, array $parameter, bool $isCollection, array $parts, string $for): array
    {
        $name = (string) ($parameter['name'] ?? '');
        $params = (array) ($parameter['params'] ?? []);

        if ($for === 'type') {
            $paramsParts = self::buildForType($propertyName, $params);
            $parts[] = 'new \\' . RecursiveHydrationStrategy::class . '('
                . 'new \\' . self::getClassHydrator($name) . '(), new ' . $name . '(' . implode(', ', $paramsParts) . '), ' . ($isCollection ? 'true' : 'false')
                . ')';
        }

        if ($for === 'strategy' && is_subclass_of(ltrim($name, '\\'), StrategyInterface::class)) {
                $paramsParts = self::buildForStrategy($propertyName, $params);
                $parts[] = 'new ' . $name . '(' . implode(', ', $paramsParts) . ')';
        }

        return $parts;
    }
}