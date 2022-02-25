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
final class Strategy
{
    /**
     * @var string
     * @Required
     */
    public string $class;

    /**
     * @var array
     * @Required
     */
    public array $parameters;

    /**
     * @throws InvalidArgumentException
     */
    public function generateCode(): string
    {
        if (!class_exists($this->class)) {
            throw new InvalidArgumentException(sprintf('Unknown class "%s" provided', $this->class));
        }

        if (!is_subclass_of($this->class, StrategyInterface::class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" must extend "%s"', $this->class, StrategyInterface::class));
        }

        return 'new ' . $this->class . '(' . implode(', ', $this->parameters) . ')';
    }
}
