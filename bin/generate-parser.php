#!/usr/bin/env php
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

use Hoa\Compiler\Llk\Llk;
use Hoa\File\Read;

require __DIR__ . '/../vendor/autoload.php';

$directory = __DIR__ . '/../src/Mordilion/GeneratedAbstractHydrator/Annotation/Type';
$compiler = Llk::load(new Read($directory . '/grammar.pp'));

file_put_contents(
    $directory . '/Parser.php',
    <<<EOS
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

namespace Mordilion\GeneratedAbstractHydrator\Annotation\Type;

/**
 * @generated Use generate-parser.php to refresh this class.
 */

EOS
    . 'final ' . Llk::save($compiler, 'Parser')
);
