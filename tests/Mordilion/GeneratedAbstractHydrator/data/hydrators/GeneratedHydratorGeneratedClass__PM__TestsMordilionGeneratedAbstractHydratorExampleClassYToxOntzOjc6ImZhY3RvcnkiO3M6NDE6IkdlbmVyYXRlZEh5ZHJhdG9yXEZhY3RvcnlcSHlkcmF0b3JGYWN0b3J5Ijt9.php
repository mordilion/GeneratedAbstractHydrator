<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Tests\Mordilion\GeneratedAbstractHydrator\ExampleClass;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Zend\Hydrator\AbstractHydrator
{
    private $hydrateCallbacks = array(), $extractCallbacks = array();
    function __construct()
    {
        parent::__construct();
        $this->hydrateCallbacks[] = \Closure::bind(static function ($object, $values, $that) {
            if (isset($values['private_string_prop']) || $object->privateStringProp !== null && \array_key_exists('private_string_prop', $values)) {
                $object->privateStringProp = (string) $that->hydrateValue('privateStringProp', $values['private_string_prop'], $object);
            }
            if (isset($values['privateIntegerProp']) || $object->privateIntegerProp !== null && \array_key_exists('privateIntegerProp', $values)) {
                $object->privateIntegerProp = (int) $that->hydrateValue('privateIntegerProp', $values['privateIntegerProp'], $object);
            }
            if (isset($values['nested']) || $object->nested !== null && \array_key_exists('nested', $values)) {
                $object->nested = $that->hydrateValue('nested', $values['nested'], $object);
            }
        }, null, 'Tests\\Mordilion\\GeneratedAbstractHydrator\\ExampleClass');
        $this->extractCallbacks[] = \Closure::bind(static function ($object, &$values, $that) {
            $values['private_string_prop'] = (string) $that->extractValue('privateStringProp', $object->privateStringProp, $object);
            $values['privateIntegerProp'] = (int) $that->extractValue('privateIntegerProp', $object->privateIntegerProp, $object);
            $values['nested'] = $that->extractValue('nested', $object->nested, $object);
        }, null, 'Tests\\Mordilion\\GeneratedAbstractHydrator\\ExampleClass');
    }
    function hydrate(array $data, $object)
    {
        if (isset($data['publicStringProp']) || $object->publicStringProp !== null && \array_key_exists('publicStringProp', $data)) {
            $object->publicStringProp = (string) $this->hydrateValue('publicStringProp', $data['publicStringProp'], $object);
        }
        if (isset($data['public_integer_prop']) || $object->publicIntegerProp !== null && \array_key_exists('public_integer_prop', $data)) {
            $object->publicIntegerProp = (int) $this->hydrateValue('publicIntegerProp', $data['public_integer_prop'], $object);
        }
        $this->hydrateCallbacks[0]->__invoke($object, $data, $this);
        return $object;
    }
    function extract($object)
    {
        $ret = [];
        $ret['publicStringProp'] = (string) $this->extractValue('publicStringProp', $object->publicStringProp, $object);
        $ret['public_integer_prop'] = (int) $this->extractValue('publicIntegerProp', $object->publicIntegerProp, $object);
        $this->extractCallbacks[0]->__invoke($object, $ret, $this);
        return $ret;
    }
}