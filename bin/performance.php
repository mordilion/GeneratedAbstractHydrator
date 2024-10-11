<?php

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\Performance;

use Composer\Autoload\ClassLoader;
use DateTime;
use GeneratedHydrator\Configuration;
use Laminas\Hydrator\AbstractHydrator;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\CodeGenerator\Annotations as GAH;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;

/** @var ClassLoader $autoloader */
$autoloader = require __DIR__ . '/../vendor/autoload.php';

$iterations = 10000;

class Example
{
    /**
     * @var DateTime
     * @GAH\Strategy(class="\Laminas\Hydrator\Strategy\DateTimeFormatterStrategy", parameters={"'Y-m-d'", "new \DateTimeZone('Europe/Berlin')"})
     */
    public $anno;
    public $foo;
    public $bar;
    private $baz;
    public $dummy10;
    public $dummy11;
    public $dummy12;
    public $dummy13;
    public $dummy14;
    public $dummy15;
    public $dummy16;
    public $dummy17;
    public $dummy18;
    public $dummy19;
    public $dummy20;
    private $dummy21;
    private $dummy22;
    private $dummy23;
    private $dummy24;
    private $dummy25;
    private $dummy26;
    private $dummy27;
    private $dummy28;
    private $dummy29;
    public function setFoo($foo) { $this->foo = $foo; }
    public function setBar($bar) { $this->bar = $bar; }
    public function setBaz($baz) { $this->baz = $baz; }
    public function getFoo() { return $this->foo; }
    public function getBar() { return $this->bar; }
    public function getBaz() { return $this->baz; }
    public function exchangeArray($data) {
        $this->foo = $data['foo']; $this->bar = $data['bar']; $this->baz = $data['baz']; $this->dummy10 = $data['dummy10']; $this->dummy11 = $data['dummy11']; $this->dummy12 = $data['dummy12']; $this->dummy13 = $data['dummy13']; $this->dummy14 = $data['dummy14']; $this->dummy15 = $data['dummy15']; $this->dummy16 = $data['dummy16']; $this->dummy17 = $data['dummy17']; $this->dummy18 = $data['dummy18']; $this->dummy19 = $data['dummy19']; $this->dummy20 = $data['dummy20']; $this->dummy21 = $data['dummy21']; $this->dummy22 = $data['dummy22']; $this->dummy23 = $data['dummy23']; $this->dummy24 = $data['dummy24']; $this->dummy25 = $data['dummy25']; $this->dummy26 = $data['dummy26']; $this->dummy27 = $data['dummy27']; $this->dummy28 = $data['dummy28']; $this->dummy29 = $data['dummy29'];
    }
    public function getArrayCopy() {
        return array('foo' => $this->foo, 'bar' => $this->bar, 'baz' => $this->baz, 'dummy10' => $this->dummy10, 'dummy11' => $this->dummy11, 'dummy12' => $this->dummy12, 'dummy13' => $this->dummy13, 'dummy14' => $this->dummy14, 'dummy15' => $this->dummy15, 'dummy16' => $this->dummy16, 'dummy17' => $this->dummy17, 'dummy18' => $this->dummy18, 'dummy19' => $this->dummy19, 'dummy20' => $this->dummy20, 'dummy21' => $this->dummy21, 'dummy22' => $this->dummy22, 'dummy23' => $this->dummy23, 'dummy24' => $this->dummy24, 'dummy25' => $this->dummy25, 'dummy26' => $this->dummy26, 'dummy27' => $this->dummy27, 'dummy28' => $this->dummy28, 'dummy29' => $this->dummy29,);
    }
}

$object = new Example();
$data = array('anno' => '2021-02-26', 'foo' => 1, 'bar' => 2, 'baz' => 3);
$dateTimeStrategy = new \Laminas\Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d');

$config = new Configuration(Example::class);
$config->setHydratorGenerator(new AbstractHydratorGenerator(PerformantAbstractHydrator::class));
$config->setGeneratedClassesTargetDir(__DIR__ . '/performance');
$config->setGeneratedClassesNamespace('PerformantAbstractHydrator');
$hydratorClass = $config->createFactory()->getHydratorClass();

/** @var PerformantAbstractHydrator $performantGeneratedHydrator */
$performantGeneratedHydrator = new $hydratorClass();
$performantGeneratedHydrator->addStrategy('anno', $dateTimeStrategy);

$config = new Configuration(Example::class);
$config->setHydratorGenerator(new AbstractHydratorGenerator(AbstractHydrator::class));
$config->setGeneratedClassesTargetDir(__DIR__ . '/performance');
$config->setGeneratedClassesNamespace('AbstractHydrator');
$hydratorClass = $config->createFactory()->getHydratorClass();

/** @var PerformantAbstractHydrator $generatedHydrator */
$generatedHydrator = new $hydratorClass();
$generatedHydrator->addStrategy('anno', $dateTimeStrategy);

$classMethodsHydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
$classMethodsHydrator->addStrategy('anno', $dateTimeStrategy);

$reflectionHydrator = new \Laminas\Hydrator\ReflectionHydrator();
$reflectionHydrator->addStrategy('anno', $dateTimeStrategy);

$arraySerializableHydrator = new \Laminas\Hydrator\ArraySerializableHydrator();
$arraySerializableHydrator->addStrategy('anno', $dateTimeStrategy);

$hydrators = array(
    $performantGeneratedHydrator,
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
