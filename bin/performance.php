<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;

/** @var ClassLoader $autloader */
$autloader = require __DIR__ . '/../vendor/autoload.php';

$iterations = 10000;

class Example
{
    /**
     * @var DateTime
     */
    public $anno;
    public $foo;
    public $bar;
    public $baz;
    public function setFoo($foo) { $this->foo = $foo; }
    public function setBar($bar) { $this->bar = $bar; }
    public function setBaz($baz) { $this->baz = $baz; }
    public function getFoo() { return $this->foo; }
    public function getBar() { return $this->bar; }
    public function getBaz() { return $this->baz; }
    public function exchangeArray($data) {
        $this->foo = $data['foo']; $this->bar = $data['bar']; $this->baz = $data['baz'];
    }
    public function getArrayCopy() {
        return array('foo' => $this->foo, 'bar' => $this->bar, 'baz' => $this->baz);
    }
}

$object = new Example();
$data = array('anno' => '2021-02-26', 'foo' => 1, 'bar' => 2, 'baz' => 3);
$config = new GeneratedHydrator\Configuration('Example');
$config->setHydratorGenerator(new AbstractHydratorGenerator(PerformantAbstractHydrator::class));
$config->setGeneratedClassesTargetDir(__DIR__ . '/performance');
$hydratorClass = $config->createFactory()->getHydratorClass();

$dateTimeStrategy = new \Laminas\Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d');

/** @var PerformantAbstractHydrator $generatedHydrator */
$generatedHydrator = new $hydratorClass();
$generatedHydrator->addStrategy('anno', $dateTimeStrategy);

$classMethodsHydrator = new Laminas\Hydrator\ClassMethodsHydrator();
$classMethodsHydrator->addStrategy('anno', $dateTimeStrategy);

$reflectionHydrator = new Laminas\Hydrator\ReflectionHydrator();
$reflectionHydrator->addStrategy('anno', $dateTimeStrategy);

$arraySerializableHydrator = new Laminas\Hydrator\ArraySerializableHydrator();
$arraySerializableHydrator->addStrategy('anno', $dateTimeStrategy);

$hydrators = array(
    $generatedHydrator,
    $classMethodsHydrator,
    $reflectionHydrator,
    $arraySerializableHydrator,
);

foreach ($hydrators as $hydrator) {
    $times = [];
    $totalStart = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $start = microtime(true);
        $hydrator->hydrate($data, $object);
        $hydrator->extract($object);
        $times[] = microtime(true) - $start;
    }

    $total = microtime(true) - $totalStart;
    echo get_class($hydrator) . PHP_EOL
        . '    Total (' . count($times) . '): ' . $total . PHP_EOL
        . '    AVG: ' . (array_sum($times) / count($times)) . PHP_EOL . PHP_EOL;
}
