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
use Mordilion\GeneratedAbstractHydrator\Annotation\Type\Parser;
use Mordilion\GeneratedAbstractHydrator\Exception\RuntimeException;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class Type
{
    public const TYPE_SIMPLE = 'simple_type';
    public const TYPE_COMPLEX = 'complex_type';

    /**
     * @Required
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $parameters;

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

        $parser = new Parser();
        $element = $parser->parse($value, 'type');
        $this->parameters = $this->determineTypeParameters($element);
    }

    public function apply(string $string, ?array $parameters = null): string
    {
        if ($parameters === null) {
            $parameters = [$this->parameters];
        }

        foreach ($parameters as $parameter) {
            if ($parameter['type'] === 'simple') {
                $string = $this->applySimpleType($string);
            }

            if ($parameter['type'] === 'complex') {
                $string = $this->apply($string, $parameter['params'] ?? null);
            }
        }

        return $string;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    private function applySimpleType(string $string): string
    {
        $name = ($this->getParameters()['name'] ?? '');
        $value = ($this->getParameters()['value'] ?? null);

        if ($name === 'array') {
            return '(array) ' . $string;
        }

        if ($name === 'string') {
            return '(string) ' . $string;
        }

        if (in_array($name, ['int', 'integer'], true)) {
            return '(int) ' . $string;
        }

        if (in_array($name, ['bool', 'boolean'], true)) {
            return '(bool) ' . $string;
        }

        if (in_array($name, ['double', 'float'], true)) {
            return '(float) ' . $string;
        }

        if (is_string($value)) {
            return $value;
        }

        return $string;
    }

    private function determineTypeParameters(TreeNode $element): array
    {
        switch ($element->getId()) {
            case '#' . self::TYPE_SIMPLE:
                return $this->getSimpleTypeParameters($element);

            case '#' . self::TYPE_COMPLEX:
                return $this->getComplexTypeParameters($element);
        }

        return [];
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
            return ['type' => 'simple', 'name' => $value];
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

        return ['type' => 'simple', 'value' => $value];
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
            'name' => $tokenNode->getValueValue(),
            'params' => array_map(
                function (TreeNode $node) {
                    return $this->determineTypeParameters($node);
                },
                $parameters
            ),
        ];
    }
}
