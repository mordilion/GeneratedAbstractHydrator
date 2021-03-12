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

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class Type
{
    public const TYPE_UNKNOWN = 'unknown';
    public const TYPE_SIMPLE_TYPECAST = 'simple_typecast';
    public const TYPE_SINGLE_OBJECT = 'single_object';
    public const TYPE_SIMPLE_ARRAY = 'simple_array';
    public const TYPE_OBJECT_ARRAY = 'object_array';

    /**
     * @Required
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]
     */
    private $simpleTypecasts = [
        'array' => '(array)',
        'bool' => '(bool)',
        'boolean' => '(bool)',
        'double' => '(float)',
        'float' => '(float)',
        'int' => '(int)',
        'integer' => '(int)',
        'string' => '(string)',
    ];

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
        $this->type = $this->determineType($value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypecast(): string
    {
        return $this->simpleTypecasts[$this->name] ?? '';
    }

    public static function getClass(string $value): string
    {
        if (class_exists($value)) {
            return $value;
        }

        return '';
    }

    private function determineType(string $value): string
    {
        if (isset($this->simpleTypecasts[$value])) {
            return self::TYPE_SIMPLE_TYPECAST;
        }

        if (class_exists($value)) {
            return self::TYPE_SINGLE_OBJECT;
        }

        if (preg_match('/array<([^<>]+)>/', $value, $matches)) {
            $value = $matches[1] ?? '';

            if (class_exists($value)) {
                return self::TYPE_OBJECT_ARRAY;
            }

            return self::TYPE_SIMPLE_ARRAY;
        }

        return self::TYPE_UNKNOWN;
    }
}
