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

use Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait StrategyEnabledTrait
{
    /**
     * @var StrategyInterface[]
     */
    private $strategies = [];

    /**
     * @param string $name
     */
    public function addStrategy($name, StrategyInterface $strategy): void
    {
        $this->strategies[$name] = $strategy;
    }

    /**
     * @param string $name
     */
    public function getStrategy($name): StrategyInterface
    {
        $strategy = $this->strategies[$name] ?? ($this->strategies['*'] ?? null);

        if ($strategy) {
            return $strategy;
        }

        throw new InvalidArgumentException(sprintf(
            '%s: no strategy by name of "%s", and no wildcard strategy present',
            __METHOD__,
            $name
        ));
    }

    /**
     * @param string $name
     */
    public function hasStrategy($name): bool
    {
        return isset($this->strategies[$name]) || isset($this->strategies['*']);
    }

    /**
     * @param string $name
     */
    public function removeStrategy($name): void
    {
        unset($this->strategies[$name]);
    }
}
