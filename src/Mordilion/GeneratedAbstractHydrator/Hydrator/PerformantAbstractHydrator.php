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

use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\NamingStrategyEnabledInterface;
use Zend\Hydrator\StrategyEnabledInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
abstract class PerformantAbstractHydrator implements HydratorInterface, StrategyEnabledInterface, NamingStrategyEnabledInterface
{
    use StrategyEnabledTrait;
    use NamingStrategyEnabledTrait;

    /**
     * @throws \Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException
     */
    public function extractName(string $name, ?object $object = null): string
    {
        if ($this->hasNamingStrategy()) {
            $name = $this->getNamingStrategy()->extract($name, $object);
        }

        return (string) $name;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws \Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException
     */
    public function extractValue(string $name, $value, ?object $object = null)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->extract($value, $object);
        }

        return $value;
    }

    /**
     * @throws \Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException
     */
    public function hydrateName(string $name, ?array $data = null): string
    {
        if ($this->hasNamingStrategy()) {
            $name = $this->getNamingStrategy()->hydrate($name, $data);
        }

        return (string) $name;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws \Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException
     */
    public function hydrateValue(string $name, $value, ?array $data = null)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->hydrate($value, $data);
        }

        return $value;
    }
}
