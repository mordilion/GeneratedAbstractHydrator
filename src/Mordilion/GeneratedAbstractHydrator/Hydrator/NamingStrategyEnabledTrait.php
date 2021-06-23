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
use Zend\Hydrator\NamingStrategy\NamingStrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait NamingStrategyEnabledTrait
{
    /**
     * @var NamingStrategyInterface|null
     */
    private $namingStrategy;

    /**
     * @return self
     */
    public function setNamingStrategy(NamingStrategyInterface $strategy)
    {
        $this->namingStrategy = $strategy;

        return $this;
    }

    /**
     * @return NamingStrategyInterface
     * @throws InvalidArgumentException
     */
    public function getNamingStrategy()
    {
        if (!$this->namingStrategy) {
            throw new InvalidArgumentException(sprintf('%s: no naming strategy present', __METHOD__));
        }

        return $this->namingStrategy;
    }

    /**
     * @return bool
     */
    public function hasNamingStrategy()
    {
        return $this->namingStrategy !== null;
    }

    /**
     * @return self
     */
    public function removeNamingStrategy()
    {
        $this->namingStrategy = null;

        return $this;
    }
}
