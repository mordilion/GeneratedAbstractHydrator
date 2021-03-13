<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Mordilion\GeneratedAbstractHydrator\ExampleClass;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Zend\Hydrator\AbstractHydrator
{
    private $hydrateCallbacks = array(), $extractCallbacks = array();
    function __construct()
    {
        parent::__construct();
        $this->setNamingStrategy(new \Zend\Hydrator\NamingStrategy\ArrayMapNamingStrategy(['publicIntegerProp' => 'public_integer_prop', 'privateStringProp' => 'private_string_prop']));
        $this->hydrateCallbacks[] = \Closure::bind(static function ($object, $data, $that) {
            $name = $that->extractName('privateStringProp', $object);
            if (isset($data[$name]) || $object->privateStringProp !== null && \array_key_exists($name, $data)) {
                $object->privateStringProp = (string) $that->hydrateValue('privateStringProp', $data[$name], $object);
            }
            $name = $that->extractName('privateIntegerProp', $object);
            if (isset($data[$name]) || $object->privateIntegerProp !== null && \array_key_exists($name, $data)) {
                $object->privateIntegerProp = (int) $that->hydrateValue('privateIntegerProp', $data[$name], $object);
            }
            $name = $that->extractName('nested', $object);
            if (isset($data[$name]) || $object->nested !== null && \array_key_exists($name, $data)) {
                $object->nested = $that->hydrateValue('nested', $data[$name], $object);
            }
        }, null, 'Mordilion\\GeneratedAbstractHydrator\\ExampleClass');
        $this->extractCallbacks[] = \Closure::bind(static function ($object, &$data, $that) {
            $name = $that->extractName('privateStringProp', $object);
            $data[$name] = (string) $that->extractValue('privateStringProp', $object->privateStringProp, $object);
            $name = $that->extractName('privateIntegerProp', $object);
            $data[$name] = (int) $that->extractValue('privateIntegerProp', $object->privateIntegerProp, $object);
            $name = $that->extractName('nested', $object);
            $data[$name] = $that->extractValue('nested', $object->nested, $object);
        }, null, 'Mordilion\\GeneratedAbstractHydrator\\ExampleClass');
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
        $data = [];
        $name = $this->extractName('publicStringProp', $object);
        $data[$name] = (string) $this->extractValue('publicStringProp', $object->publicStringProp, $object);
        $name = $this->extractName('publicIntegerProp', $object);
        $data[$name] = (int) $this->extractValue('publicIntegerProp', $object->publicIntegerProp, $object);
        $this->extractCallbacks[0]->__invoke($object, $data, $this);
        return $data;
    }
}