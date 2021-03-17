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
use Zend\Hydrator\NamingStrategy\NamingStrategyInterface;
use Zend\Hydrator\NamingStrategyEnabledInterface;
use Zend\Hydrator\StrategyEnabledInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
abstract class AbstractHydrator implements HydratorInterface, StrategyEnabledInterface, NamingStrategyEnabledInterface
{
    use StrategyEnabledTrait;
    use NamingStrategyEnabledTrait;

    public function __construct()
    {
    }

    protected function extractValue($name, $value, $object = null)
    {
        if (!$this->hasStrategy($name) && $this->hasNamingStrategy()) {
            /** @var NamingStrategyInterface $namingStrategy */
            $namingStrategy = $this->getNamingStrategy();
            $name = $namingStrategy->hydrate($name);
        }

        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);

            return $strategy->extract($value, $object);
        }

        return $value;
    }

    protected function hydrateValue($name, $value, $data = null)
    {
        if (!$this->hasStrategy($name) && $this->hasNamingStrategy()) {
            /** @var NamingStrategyInterface $namingStrategy */
            $namingStrategy = $this->getNamingStrategy();
            $name = $namingStrategy->hydrate($name);
        }

        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);

            return $strategy->hydrate($value, $data);
        }

        return $value;
    }
}
