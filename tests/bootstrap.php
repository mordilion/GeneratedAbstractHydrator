<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$files = glob(__DIR__ . '/Mordilion/GeneratedAbstractHydrator/data/hydrators/*');

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
