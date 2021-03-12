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

namespace Mordilion\GeneratedAbstractHydrator\Annotation;

use Mordilion\GeneratedAbstractHydrator\Exception\RuntimeException;
use function is_string;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class SerializedName
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param mixed[] $values
     *
     * @throws RuntimeException
     */
    public function __construct(array $values)
    {
        $value = $values['value'] ?? null;

        if (!is_string($value)) {
            throw new RuntimeException(sprintf('"value" must be a string.'));
        }

        $this->name = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
