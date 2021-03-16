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
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var object
     */
    private $object;

    /**
     * @var bool
     */
    private $isCollection;

    /**
     * @var bool
     */
    private $allowNull;

    public function __construct(HydratorInterface $hydrator, object $object, bool $isCollection = false, bool $allowNull = true)
    {
        $this->hydrator = $hydrator;
        $this->object = $object;
        $this->isCollection = $isCollection;
        $this->allowNull = $allowNull;
    }

    /**
     * @param mixed $value
     *
     * @return array|array[]|null
     * @throws InvalidArgumentException
     */
    public function extract($value): ?array
    {
        if (!$this->isCollection) {
            return $this->extractObject($value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $collection = [];

        foreach ($value as $item) {
            $collection[] = $this->extractObject($item);
        }

        return $collection;
    }

    /**
     * @param mixed $value
     *
     * @return object|object[]
     */
    public function hydrate($value)
    {
        if (!$this->isCollection) {
            return $this->hydrateObject($value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $collection = [];

        foreach ($value as $item) {
            $collection[] = $this->hydrateObject($item);
        }

        return $collection;
    }

    /**
     * @param mixed $value
     *
     * @return array|null
     * @throws InvalidArgumentException
     */
    private function extractObject($value): ?array
    {
        if (!$value instanceof $this->object && !($this->allowNull && $value === null)) {
            throw new InvalidArgumentException('$value is not an instance of "' . get_class($this->object) . '".');
        }

        if ($value === null) {
            return null;
        }

        return $this->hydrator->extract($value);
    }

    /**
     * @param mixed $value
     */
    private function hydrateObject($value): object
    {
        $instance = clone $this->object;

        return $this->hydrator->hydrate($value, $instance);
    }
}
