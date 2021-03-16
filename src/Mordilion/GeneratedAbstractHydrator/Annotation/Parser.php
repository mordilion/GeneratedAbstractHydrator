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

/**
 * @generated Use generate-parser.php to refresh this class.
 */
final class Parser extends \Hoa\Compiler\Llk\Parser
{
    public function __construct()
    {
        parent::__construct(
            [
                'default' => [
                    'skip' => '\s+',
                    'parenthesis_' => '<',
                    '_parenthesis' => '>',
                    'bracket_' => '\(',
                    '_bracket' => '\)',
                    'empty_string' => '""|\'\'',
                    'number' => '(\+|\-)?(0|[1-9]\d*)(\.\d+)?',
                    'null' => 'null',
                    'comma' => ',',
                    'name' => '(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*',
                    'quote_:quoted_string' => '"',
                    'apostrophe_:apostrophed_string' => '\'',
                ],
                'quoted_string' => [
                    'quoted_string' => '[^"]+',
                    '_quote:default' => '"',
                ],
                'apostrophed_string' => [
                    'apostrophed_string' => '[^\']+',
                    '_apostrophe:default' => '\'',
                ],
            ],
            [
                'type' => new \Hoa\Compiler\Llk\Rule\Choice('type', ['simple_type', 'object_type', 'complex_type'], null),
                1 => new \Hoa\Compiler\Llk\Rule\Token(1, 'name', null, -1, true),
                2 => new \Hoa\Compiler\Llk\Rule\Concatenation(2, [1], '#simple_type'),
                3 => new \Hoa\Compiler\Llk\Rule\Token(3, 'number', null, -1, true),
                4 => new \Hoa\Compiler\Llk\Rule\Concatenation(4, [3], '#simple_type'),
                5 => new \Hoa\Compiler\Llk\Rule\Token(5, 'null', null, -1, true),
                6 => new \Hoa\Compiler\Llk\Rule\Concatenation(6, [5], '#simple_type'),
                7 => new \Hoa\Compiler\Llk\Rule\Token(7, 'empty_string', null, -1, true),
                8 => new \Hoa\Compiler\Llk\Rule\Concatenation(8, [7], '#simple_type'),
                9 => new \Hoa\Compiler\Llk\Rule\Token(9, 'quote_', null, -1, false),
                10 => new \Hoa\Compiler\Llk\Rule\Token(10, 'quoted_string', null, -1, true),
                11 => new \Hoa\Compiler\Llk\Rule\Token(11, '_quote', null, -1, false),
                12 => new \Hoa\Compiler\Llk\Rule\Concatenation(12, [9, 10, 11], '#simple_type'),
                13 => new \Hoa\Compiler\Llk\Rule\Token(13, 'apostrophe_', null, -1, false),
                14 => new \Hoa\Compiler\Llk\Rule\Token(14, 'apostrophed_string', null, -1, true),
                15 => new \Hoa\Compiler\Llk\Rule\Token(15, '_apostrophe', null, -1, false),
                16 => new \Hoa\Compiler\Llk\Rule\Concatenation(16, [13, 14, 15], '#simple_type'),
                'simple_type' => new \Hoa\Compiler\Llk\Rule\Choice('simple_type', [2, 4, 6, 8, 12, 16], null),
                18 => new \Hoa\Compiler\Llk\Rule\Token(18, 'name', null, -1, true),
                19 => new \Hoa\Compiler\Llk\Rule\Token(19, 'bracket_', null, -1, false),
                20 => new \Hoa\Compiler\Llk\Rule\Token(20, 'comma', null, -1, false),
                21 => new \Hoa\Compiler\Llk\Rule\Concatenation(21, [20, 'type'], '#object_type'),
                22 => new \Hoa\Compiler\Llk\Rule\Repetition(22, 0, -1, 21, null),
                23 => new \Hoa\Compiler\Llk\Rule\Token(23, '_bracket', null, -1, false),
                'object_type' => new \Hoa\Compiler\Llk\Rule\Concatenation('object_type', [18, 19, 'type', 22, 23], null),
                25 => new \Hoa\Compiler\Llk\Rule\Token(25, 'name', null, -1, true),
                26 => new \Hoa\Compiler\Llk\Rule\Token(26, 'parenthesis_', null, -1, false),
                27 => new \Hoa\Compiler\Llk\Rule\Token(27, 'comma', null, -1, false),
                28 => new \Hoa\Compiler\Llk\Rule\Concatenation(28, [27, 'type'], '#complex_type'),
                29 => new \Hoa\Compiler\Llk\Rule\Repetition(29, 0, -1, 28, null),
                30 => new \Hoa\Compiler\Llk\Rule\Token(30, '_parenthesis', null, -1, false),
                'complex_type' => new \Hoa\Compiler\Llk\Rule\Concatenation('complex_type', [25, 26, 'type', 29, 30], null),
            ],
            [
            ]
        );

        $this->getRule('type')->setPPRepresentation(' simple_type() | object_type() | complex_type()');
        $this->getRule('simple_type')->setDefaultId('#simple_type');
        $this->getRule('simple_type')->setPPRepresentation(' <name> | <number> | <null> | <empty_string> | ::quote_:: <quoted_string> ::_quote:: | ::apostrophe_:: <apostrophed_string> ::_apostrophe::');
        $this->getRule('object_type')->setDefaultId('#object_type');
        $this->getRule('object_type')->setPPRepresentation(' <name> ::bracket_:: type() ( ::comma:: type() )* ::_bracket::');
        $this->getRule('complex_type')->setDefaultId('#complex_type');
        $this->getRule('complex_type')->setPPRepresentation(' <name> ::parenthesis_:: type() ( ::comma:: type() )* ::_parenthesis::');
    }
}
