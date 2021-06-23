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

namespace Mordilion\GeneratedAbstractHydrator\Strategy;

use Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class RecursiveHydrationStrategy implements StrategyInterface
{
    public const TYPE_UNKNOWN = 'unknown';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_FLOAT = 'float';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_STRING = 'string';
    public const TYPE_OBJECT = 'object';

    public const TYPE_ALL = [
        self::TYPE_BOOLEAN,
        self::TYPE_FLOAT,
        self::TYPE_INTEGER,
        self::TYPE_STRING,
    ];

    private const TYPE_MAP = [
        'bool' => self::TYPE_BOOLEAN,
        'double' => self::TYPE_FLOAT,
        'int' => self::TYPE_INTEGER,
    ];

    /**
     * @var bool
     */
    private $isCollection;

    /**
     * @var ?HydratorInterface
     */
    private $hydrator;

    /**
     * @var object
     */
    private $object;

    /**
     * @var string
     */
    private $type = self::TYPE_UNKNOWN;

    /**
     * @param string|object $objectOrType
     *
     * @throws InvalidArgumentException
     */
    public function __construct($objectOrType, ?HydratorInterface $hydrator, bool $isCollection = false)
    {
        $this->hydrator = $hydrator;
        $this->isCollection = $isCollection;

        if (is_string($objectOrType)) {
            $objectOrType = self::TYPE_MAP[$objectOrType] ?? $objectOrType;
        }

        if (!is_object($objectOrType) && !in_array($objectOrType, self::TYPE_ALL, true)) {
            throw new InvalidArgumentException('$objectOrType must be an object or a string.');
        }

        if (is_object($objectOrType)) {
            $this->object = $objectOrType;
            $this->type = self::TYPE_OBJECT;
        }

        if (is_string($objectOrType)) {
            $this->type = $objectOrType;
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function extract($value)
    {
        if ($value === null) {
            return $this->isCollection ? [] : $value;
        }

        if (!$this->isCollection) {
            return $this->extractType($value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $collection = [];

        foreach ($value as $item) {
            $collection[] = $this->extractType($item);
        }

        return $collection;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function hydrate($value)
    {
        if (!$this->isCollection) {
            return $this->hydrateType($value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $collection = [];

        foreach ($value as $item) {
            $collection[] = $this->hydrateType($item);
        }

        return $collection;
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    private function extractObject($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof $this->object) {
            throw new InvalidArgumentException('$value is not an instance of "' . get_class($this->object) . '".');
        }

        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidArgumentException('No hydrator provided!');
        }

        return $this->hydrator->extract($value);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function extractType($value)
    {
        if ($this->type === self::TYPE_OBJECT) {
            return $this->extractObject($value);
        }

        return $this->convertType($value);
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    private function hydrateObject($value): ?object
    {
        if ($value === null) {
            return null;
        }

        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidArgumentException('No hydrator provided!');
        }

        $instance = clone $this->object;

        return $this->hydrator->hydrate($value, $instance);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function hydrateType($value)
    {
        if ($this->type === self::TYPE_OBJECT) {
            return $this->hydrateObject($value);
        }

        return $this->convertType($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool|float|int|string|null
     */
    private function convertType($value)
    {
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
                return (bool) $value;

            case self::TYPE_FLOAT:
                return (float) $value;

            case self::TYPE_INTEGER:
                return (int) $value;

            case self::TYPE_STRING:
                return (string) $value;
        }

        return null;
    }
}
