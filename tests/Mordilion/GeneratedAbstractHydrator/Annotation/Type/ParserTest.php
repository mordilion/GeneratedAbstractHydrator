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

namespace Tests\Mordilion\GeneratedAbstractHydrator\Annotation\Type;

use Mordilion\GeneratedAbstractHydrator\Annotation\Type;
use PHPUnit\Framework\TestCase;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
final class ParserTest extends TestCase
{
    public function testComplexTypeParsing(): void
    {
        $type = new Type(['value' => 'array<string, integer>']);
        self::assertEquals(['type' => 'complex', 'data' => ['type' => 'simple', 'name' => 'array'], 'params' => [['type' => 'simple', 'name' => 'string'], ['type' => 'simple', 'name' => 'integer']]], $type->getParameters());

        $type = new Type(['value' => 'array<DateTimeImmutable>']);
        self::assertEquals(['type' => 'complex', 'data' => ['type' => 'simple', 'name' => 'array'], 'params' => [['type' => 'object', 'name' => '\\DateTimeImmutable']]], $type->getParameters());

        $type = new Type(['value' => 'ArrayCollection<DateTimeImmutable>']);
        self::assertEquals(['type' => 'complex', 'data' => ['type' => 'simple', 'name' => 'ArrayCollection'], 'params' => [['type' => 'object', 'name' => '\\DateTimeImmutable']]], $type->getParameters());
    }

    public function testObjectTypeParsing(): void
    {
        $type = new Type(['value' => DateTimeFormatterStrategy::class . "('Y-m-d')"]);
        self::assertEquals(['type' => 'object', 'name' => '\\' . DateTimeFormatterStrategy::class, 'params' => [['type' => 'simple', 'value' => 'Y-m-d', 'quote' => '\'']]], $type->getParameters());
    }

    public function testSimpleTypeParsing(): void
    {
        $type = new Type(['value' => 'boolean']);
        self::assertEquals(['type' => 'simple', 'name' => 'boolean'], $type->getParameters());

        $type = new Type(['value' => 'integer']);
        self::assertEquals(['type' => 'simple', 'name' => 'integer'], $type->getParameters());

        $type = new Type(['value' => 'null']);
        self::assertEquals(['type' => 'simple', 'value' => 'null'], $type->getParameters());

        $type = new Type(['value' => '16.5']);
        self::assertEquals(['type' => 'simple', 'value' => '16.5'], $type->getParameters());
    }
}
