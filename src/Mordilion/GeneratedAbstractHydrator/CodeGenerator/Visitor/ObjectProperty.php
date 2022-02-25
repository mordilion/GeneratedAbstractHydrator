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

use ReflectionProperty;

use function array_key_exists;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 *
 * @internal
 */
final class ObjectProperty
{
    public bool $hasType;

    public bool $hasDefault;

    public bool $allowsNull;

    /**
     * @psalm-var non-empty-string
     */
    public string $name;

    /**
     * @psalm-param non-empty-string $name
     */
    private function __construct(string $name, bool $hasType, bool $allowsNull, bool $hasDefault)
    {
        $this->name = $name;
        $this->hasType = $hasType;
        $this->allowsNull = $allowsNull;
        $this->hasDefault = $hasDefault;
    }

    public static function fromReflection(ReflectionProperty $property): self
    {
        /** @psalm-var non-empty-string $propertyName */
        $propertyName = $property->getName();
        $type = $property->getType();
        $hasDefault = array_key_exists($propertyName, $property->getDeclaringClass()->getDefaultProperties());

        if ($type === null) {
            return new self($propertyName, false, true, $hasDefault);
        }

        return new self($propertyName, true, $type->allowsNull(), $hasDefault);
    }
}
