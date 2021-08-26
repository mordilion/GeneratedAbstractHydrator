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
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait StrategyEnabledTrait
{
    /**
     * @var StrategyInterface[]
     */
    private array $strategies = [];

    public function addStrategy(string $name, StrategyInterface $strategy): void
    {
        $this->strategies[$name] = $strategy;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getStrategy(string $name): StrategyInterface
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

    public function hasStrategy(string $name): bool
    {
        return isset($this->strategies[$name]) || isset($this->strategies['*']);
    }

    public function removeStrategy(string $name): void
    {
        unset($this->strategies[$name]);
    }
}
