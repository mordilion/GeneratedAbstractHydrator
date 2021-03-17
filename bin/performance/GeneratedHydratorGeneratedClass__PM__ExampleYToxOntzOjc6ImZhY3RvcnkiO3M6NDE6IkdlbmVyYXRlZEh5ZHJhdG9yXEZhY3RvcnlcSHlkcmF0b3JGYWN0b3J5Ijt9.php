<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Example;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Mordilion\GeneratedAbstractHydrator\Hydrator\AbstractHydrator
{
    private $hydrateCallbacks = array(), $extractCallbacks = array();
    function __construct()
    {
        parent::__construct();
        $this->addStrategy('anno', new \Zend\Hydrator\Strategy\StrategyChain([new \Zend\Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d')]));
    }
    function hydrate(array $data, $object)
    {
        $name = 'anno';
        if (isset($data[$name]) || $object->anno !== null && \array_key_exists($name, $data)) {
            $object->anno = $this->hydrateValue('anno', $data[$name], $object);
        }
        $name = 'foo';
        if (isset($data[$name]) || $object->foo !== null && \array_key_exists($name, $data)) {
            $object->foo = $this->hydrateValue('foo', $data[$name], $object);
        }
        $name = 'bar';
        if (isset($data[$name]) || $object->bar !== null && \array_key_exists($name, $data)) {
            $object->bar = $this->hydrateValue('bar', $data[$name], $object);
        }
        $name = 'baz';
        if (isset($data[$name]) || $object->baz !== null && \array_key_exists($name, $data)) {
            $object->baz = $this->hydrateValue('baz', $data[$name], $object);
        }
        return $object;
    }
    function extract($object)
    {
        $data = [];
        $name = 'anno';
        $data[$name] = $this->extractValue('anno', $object->anno, $object);
        $name = 'foo';
        $data[$name] = $this->extractValue('foo', $object->foo, $object);
        $name = 'bar';
        $data[$name] = $this->extractValue('bar', $object->bar, $object);
        $name = 'baz';
        $data[$name] = $this->extractValue('baz', $object->baz, $object);
        return $data;
    }
}