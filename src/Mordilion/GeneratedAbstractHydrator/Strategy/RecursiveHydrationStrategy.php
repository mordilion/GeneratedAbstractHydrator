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
     * @var string
     */
    private $className;

    /**
     * @var bool
     */
    private $isCollection;

    public function __construct(HydratorInterface $hydrator, string $className, bool $isCollection = false)
    {
        $this->hydrator = $hydrator;
        $this->className = $className;
        $this->isCollection = $isCollection;
    }

    /**
     * @param mixed $value
     *
     * @return array|mixed
     */
    public function extract($value)
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
     * @return mixed|object
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
     * @return array|mixed
     */
    private function extractObject($value)
    {
        if (!$value instanceof $this->className) {
            throw new \InvalidArgumentException('The $value is not an instance of "' . $this->className . '".');
        }

        return $this->hydrator->extract($value);
    }

    /**
     * @param mixed $value
     *
     * @return object|mixed
     */
    private function hydrateObject($value)
    {
        $instance = new $this->className();

        return $this->hydrator->hydrate($value, $instance);
    }
}
