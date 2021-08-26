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
use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait NamingStrategyEnabledTrait
{
    private ?NamingStrategyInterface $namingStrategy = null;

    public function setNamingStrategy(NamingStrategyInterface $strategy): self
    {
        $this->namingStrategy = $strategy;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getNamingStrategy(): NamingStrategyInterface
    {
        if (!$this->namingStrategy) {
            throw new InvalidArgumentException(sprintf('%s: no naming strategy present', __METHOD__));
        }

        return $this->namingStrategy;
    }

    public function hasNamingStrategy(): bool
    {
        return $this->namingStrategy !== null;
    }

    public function removeNamingStrategy(): self
    {
        $this->namingStrategy = null;

        return $this;
    }
}
