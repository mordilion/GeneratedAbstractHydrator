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

use Zend\Hydrator\NamingStrategy\NamingStrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait NamingStrategyEnabledTrait
{
    /**
     * @var NamingStrategyInterface
     */
    private $strategy;

    public function setNamingStrategy(NamingStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getNamingStrategy(): ?NamingStrategyInterface
    {
        return $this->strategy;
    }

    public function hasNamingStrategy(): bool
    {
        return $this->strategy !== null;
    }

    public function removeNamingStrategy(): void
    {
        unset($this->strategy);
    }
}
