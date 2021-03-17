<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Annotation as GHA;

/** @var ClassLoader $autloader */
$autloader = require __DIR__ . '/../vendor/autoload.php';

$iterations = 10000;

class Example
{
    /**
     * @GHA\Type("DateTime<'Y-m-d'>")
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
$config->setHydratorGenerator(new AbstractHydratorGenerator());
$config->setGeneratedClassesTargetDir(__DIR__ . '/performance');
$hydratorClass = $config->createFactory()->getHydratorClass();

$hydrators = array(
    new $hydratorClass(),
    new Zend\Hydrator\ClassMethods(),
    new Zend\Hydrator\Reflection(),
    new Zend\Hydrator\ArraySerializable(),
);

foreach ($hydrators as $hydrator) {
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i += 1) {
        $hydrator->hydrate($data, $object);
        $hydrator->extract($object);
    }

    var_dump(microtime(true) - $start);
}
