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

use Hoa\Compiler\Llk\TreeNode;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
final class ParserHelper
{
    private const TYPE_COMPLEX = 'complex_type';
    private const TYPE_OBJECT = 'object_type';
    private const TYPE_SIMPLE = 'simple_type';

    public function parse(Parser $parser, string $string, string $rule): array
    {
        return $this->determineParameters($parser->parse($string, $rule));
    }

    private function determineParameters(TreeNode $element): array
    {
        switch ($element->getId()) {
            case '#' . self::TYPE_COMPLEX:
                return $this->getComplexTypeParameters($element);

            case '#' . self::TYPE_OBJECT:
                return $this->getObjectTypeParameters($element);

            case '#' . self::TYPE_SIMPLE:
                return $this->getSimpleTypeParameters($element);
        }

        return [];
    }

    private function getComplexTypeParameters(TreeNode $element): array
    {
        $tokenNode = $element->getChild(0);

        if ($tokenNode === null) {
            return [];
        }

        $parameters = array_slice($element->getChildren(), 1);

        return [
            'type' => 'complex',
            //'name' => $tokenNode->getValueValue(),
            'data' => $this->getSimpleTypeParameters($element),
            'params' => array_map(
                function (TreeNode $node) {
                    return $this->determineParameters($node);
                },
                $parameters
            ),
        ];
    }

    private function getObjectTypeParameters(TreeNode $element): array
    {
        $tokenNode = $element->getChild(0);

        if ($tokenNode === null) {
            return [];
        }

        $parameters = array_slice($element->getChildren(), 1);

        return [
            'type' => 'object',
            'name' => '\\' . $tokenNode->getValueValue(),
            'params' => array_map(
                function (TreeNode $node) {
                    return $this->determineParameters($node);
                },
                $parameters
            ),
        ];
    }

    private function getSimpleTypeParameters(TreeNode $element): array
    {
        $tokenNode = $element->getChild(0);

        if ($tokenNode === null) {
            return [];
        }

        $token = $tokenNode->getValueToken();
        $value = $tokenNode->getValueValue();

        if ($token === 'name') {
            $type = class_exists('\\' . ltrim($value, '\\')) ? 'object' : 'simple';

            return ['type' => $type, 'name' => $type === 'object' ? '\\' . ltrim($value, '\\') : $value];
        }

        if ($token === 'empty_string') {
            return ['type' => 'simple', 'value' => ''];
        }

        if ($token === 'null') {
            return ['type' => 'simple', 'value' => 'null'];
        }

        if ($token === 'number') {
            return ['type' => 'simple', 'value' => $value];
        }

        $escapeChar = $token === 'quoted_string' ? '"' : "'";

        if (strpos($value, $escapeChar) !== false) {
            $value = str_replace($escapeChar . $escapeChar, $escapeChar, $value);
        }

        return ['type' => 'simple', 'value' => $value, 'quote' => $escapeChar];
    }
}
