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

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
class StringStrategy implements StrategyInterface
{
    public function extract($value)
    {
        return (string) $value;
    }

    public function hydrate($value)
    {
        return (string) $value;
    }
}
