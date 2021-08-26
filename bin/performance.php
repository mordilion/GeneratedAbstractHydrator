<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;
use Zend\Hydrator\AbstractHydrator;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

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
$performantHydratorClass = $config->createFactory()->getHydratorClass();

$dateTimeStrategy = new DateTimeFormatterStrategy('Y-m-d');

/** @var PerformantAbstractHydrator $generatedHydrator */
$generatedHydrator = new $performantHydratorClass();
$generatedHydrator->addStrategy('anno', $dateTimeStrategy);

$classMethodsHydrator = new Zend\Hydrator\ClassMethods();
$classMethodsHydrator->addStrategy('anno', $dateTimeStrategy);

$reflectionHydrator = new Zend\Hydrator\Reflection();
$reflectionHydrator->addStrategy('anno', $dateTimeStrategy);

$arraySerializableHydrator = new Zend\Hydrator\ArraySerializable();
$arraySerializableHydrator->addStrategy('anno', $dateTimeStrategy);

$hydrators = array(
    $generatedHydrator,
    $classMethodsHydrator,
    $reflectionHydrator,
    $arraySerializableHydrator,
);

foreach ($hydrators as $generatedHydrator) {
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $generatedHydrator->hydrate($data, $object);
        $generatedHydrator->extract($object);
    }

    echo get_class($generatedHydrator) . ': ' . PHP_EOL . '    - ' . (microtime(true) - $start) . PHP_EOL . PHP_EOL;
}
