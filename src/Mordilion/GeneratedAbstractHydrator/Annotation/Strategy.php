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
 * @Target({"PROPERTY", "ANNOTATION"})
 */
final class Strategy implements ParserAwareInterface
{
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
        $parserHelper = new ParserHelper();
        $this->parameters = $parserHelper->parse($parser, $value, 'type');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
