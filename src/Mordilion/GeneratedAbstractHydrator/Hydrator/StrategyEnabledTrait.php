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

    public function addStrategy($name, StrategyInterface $strategy): void
    {
        $this->strategies[$name] = $strategy;
    }

    public function getStrategy($name): ?StrategyInterface
    {
        return $this->strategies[$name] ?? ($this->strategies['*'] ?? null);
    }

    public function hasStrategy($name): bool
    {
        return isset($this->strategies[$name]) || isset($this->strategies['*']);
    }

    public function removeStrategy($name): void
    {
        unset($this->strategies[$name]);
    }
}
