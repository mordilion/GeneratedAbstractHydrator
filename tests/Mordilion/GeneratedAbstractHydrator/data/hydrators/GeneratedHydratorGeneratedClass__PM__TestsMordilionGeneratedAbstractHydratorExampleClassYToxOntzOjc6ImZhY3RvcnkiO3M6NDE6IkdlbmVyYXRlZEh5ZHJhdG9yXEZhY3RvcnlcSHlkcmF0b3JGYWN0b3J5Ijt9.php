<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Tests\Mordilion\GeneratedAbstractHydrator\ExampleClass;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Mordilion\GeneratedAbstractHydrator\AbstractHydrator
{
    private $hydrateCallbacks = array(), $extractCallbacks = array();
    function __construct()
    {
        parent::__construct();
        $this->setNamingStrategy(new \Zend\Hydrator\NamingStrategy\ArrayMapNamingStrategy(['publicIntegerProp' => 'public_integer_prop', 'privateStringProp' => 'private_string_prop']));
        $this->hydrateCallbacks[] = \Closure::bind(static function ($object, $values, $that) {
            $name = $that->extractName('privateStringProp', $object);
            if (isset($values[$name]) || $object->privateStringProp !== null && \array_key_exists($name, $values)) {
                $object->privateStringProp = (string) $that->hydrateValue('privateStringProp', $values[$name], $object);
            }
            $name = $that->extractName('privateIntegerProp', $object);
            if (isset($values[$name]) || $object->privateIntegerProp !== null && \array_key_exists($name, $values)) {
                $object->privateIntegerProp = (int) $that->hydrateValue('privateIntegerProp', $values[$name], $object);
            }
            $name = $that->extractName('nested', $object);
            if (isset($values[$name]) || $object->nested !== null && \array_key_exists($name, $values)) {
                $object->nested = $that->hydrateValue('nested', $values[$name], $object);
            }
        }, null, 'Tests\\Mordilion\\GeneratedAbstractHydrator\\ExampleClass');
        $this->extractCallbacks[] = \Closure::bind(static function ($object, &$values, $that) {
            $name = $that->extractName('privateStringProp', $object);
            $values[$name] = (string) $that->extractValue('privateStringProp', $object->privateStringProp, $object);
            $name = $that->extractName('privateIntegerProp', $object);
            $values[$name] = (int) $that->extractValue('privateIntegerProp', $object->privateIntegerProp, $object);
            $name = $that->extractName('nested', $object);
            $values[$name] = $that->extractValue('nested', $object->nested, $object);
        }, null, 'Tests\\Mordilion\\GeneratedAbstractHydrator\\ExampleClass');
    }
    function hydrate(array $data, $object)
    {
        $name = $this->extractName('publicStringProp', $object);
        if (isset($data[$name]) || $object->publicStringProp !== null && \array_key_exists($name, $data)) {
            $object->publicStringProp = (string) $this->hydrateValue('publicStringProp', $data[$name], $object);
        }
        $name = $this->extractName('publicIntegerProp', $object);
        if (isset($data[$name]) || $object->publicIntegerProp !== null && \array_key_exists($name, $data)) {
            $object->publicIntegerProp = (int) $this->hydrateValue('publicIntegerProp', $data[$name], $object);
        }
        $this->hydrateCallbacks[0]->__invoke($object, $data, $this);
        return $object;
    }
    function extract($object)
    {
        $ret = [];
        $name = $this->extractName('publicStringProp', $object);
        $ret[$name] = (string) $this->extractValue('publicStringProp', $object->publicStringProp, $object);
        $name = $this->extractName('publicIntegerProp', $object);
        $ret[$name] = (int) $this->extractValue('publicIntegerProp', $object->publicIntegerProp, $object);
        $this->extractCallbacks[0]->__invoke($object, $ret, $this);
        return $ret;
    }
}