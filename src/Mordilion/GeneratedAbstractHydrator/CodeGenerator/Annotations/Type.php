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

namespace Mordilion\GeneratedAbstractHydrator\CodeGenerator\Annotations;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Mordilion\GeneratedAbstractHydrator\Exception\InvalidArgumentException;

/**
 * @author Henning Huncke <henning.huncke@check24.de>
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Type
{
    /**
     * @var string
     */
    public string $name;

    public function __construct($values = [])
    {
        var_dump($values);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function generateCode(): string
    {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Type must be set');
        }

        return 'new ' . $this->class . '(' . implode(', ', $this->parameters) . ')';
    }
}
