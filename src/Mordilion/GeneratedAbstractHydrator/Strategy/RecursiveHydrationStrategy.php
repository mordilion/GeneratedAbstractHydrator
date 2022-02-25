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

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class RecursiveHydrationStrategy implements StrategyInterface
{
    private HydratorInterface $hydrator;

    private bool $isCollection;

    private object $object;

    public function __construct(object $object, HydratorInterface $hydrator, bool $isCollection = false)
    {
        $this->hydrator = $hydrator;
        $this->isCollection = $isCollection;
        $this->object = $object;
    }

    /**
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function extract($value, ?object $object = null)
    {
        if ($value === null) {
            return $this->isCollection ? [] : $value;
        }

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
     */
    public function hydrate($value, ?array $data)
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

        return $this->hydrator->extract($value);
    }

    /**
     * @param mixed $value
     */
    private function hydrateObject($value): ?object
    {
        if ($value === null) {
            return null;
        }

        $instance = clone $this->object;

        return $this->hydrator->hydrate($value, $instance);
    }
}
