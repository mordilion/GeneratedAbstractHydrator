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

namespace Mordilion\GeneratedAbstractHydrator;

use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\Annotation as GHA;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Zend\Hydrator\AbstractHydrator;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
final class GeneralAnnotationTest extends TestCase
{
    public function testCommonHydrateCase(): void
    {
        $hydrator = $this->getClassHydrator(ExampleClass::class);
        $data = [
            'publicStringProp' => 'This is a string property!',
            'public_integer_prop' => '1024',
            'private_string_prop' => 'This is another string property!',
            'privateIntegerProp' => '2048',
            'nested' => [
                'prop1' => 'Test for Prop1',
                'prop2' => 'this_is_false',
            ],
        ];
        $object = new ExampleClass('', 0, '', 0, null);

        $hydrator->hydrate($data, $object);
        $reflection = new ReflectionClass($object);

        self::assertEquals($data['publicStringProp'], $object->publicStringProp);
        self::assertEquals($data['public_integer_prop'], $object->publicIntegerProp);
        self::assertIsInt($object->publicIntegerProp);

        $property = $reflection->getProperty('privateStringProp');
        $property->setAccessible(true);
        self::assertEquals($data['private_string_prop'], $property->getValue($object));

        $property = $reflection->getProperty('privateIntegerProp');
        $property->setAccessible(true);
        self::assertEquals($data['privateIntegerProp'], $property->getValue($object));
        self::assertIsInt($property->getValue($object));

        $nested = $object->nested;
        self::assertEquals($data['nested']['prop1'], $nested->prop1);
        self::assertEquals(false, $nested->prop2);
        self::assertIsString($nested->prop1);
        self::assertIsBool($nested->prop2);
    }

    public function testCommonExtractCase()
    {
        $hydrator = $this->getClassHydrator(ExampleClass::class);
        $object = new ExampleClass(
            'This is a string property!',
            1024,
            'This is another string property!',
            2048,
            null
        );

        $data = $hydrator->extract($object);
        $reflection = new ReflectionClass($object);

        self::assertEquals($object->publicStringProp, $data['publicStringProp']);
        self::assertEquals($object->publicIntegerProp, $data['public_integer_prop']);

        $property = $reflection->getProperty('privateStringProp');
        $property->setAccessible(true);
        self::assertEquals($property->getValue($object), $data['private_string_prop']);

        $property = $reflection->getProperty('privateIntegerProp');
        $property->setAccessible(true);
        self::assertEquals($property->getValue($object), $data['privateIntegerProp']);
    }

    private function getClassHydrator(string $class): AbstractHydrator
    {
        $config = new Configuration($class);
        $config->setHydratorGenerator(new AbstractHydratorGenerator());
        $hydratorClass = $config->createFactory()->getHydratorClass();

        if (!class_exists($hydratorClass)) {
            throw new RuntimeException('Could not create Hydrator!');
        }

        /** @var AbstractHydrator $hydrator */
        $hydrator = new $hydratorClass();

        return $hydrator;
    }
}

// ---
final class ExampleClass
{
    /**
     * @GHA\Type("array<DateTime('now')>")
     * @var array
     */
    public $simpleCollection = [];

    /**
     * @GHA\Type("string")
     * @var string
     */
    public $publicStringProp;

    /**
     * @GHA\Type("int")
     * @GHA\SerializedName("public_integer_prop")
     * @var int
     */
    public $publicIntegerProp;

    /**
     * @GHA\Type("string")
     * @GHA\SerializedName("private_string_prop")
     * @var string
     */
    private $privateStringProp;

    /**
     * @GHA\Type("integer")
     * @var int
     */
    private $privateIntegerProp;

    /**
     * @GHA\Type("Mordilion\GeneratedAbstractHydrator\NestedClass")
     * @var NestedClass|null
     */
    public $nested;

    public function __construct(string $publicStringProp, int $publicIntegerProp, string $privateStringProp, int $privateIntegerProp, ?NestedClass $nested)
    {
        $this->publicStringProp = $publicStringProp;
        $this->publicIntegerProp = $publicIntegerProp;
        $this->privateStringProp = $privateStringProp;
        $this->privateIntegerProp = $privateIntegerProp;
        $this->nested = $nested;
    }
}

final class NestedClass
{
    /**
     * @GHA\Type("string")
     * @var string
     */
    public $prop1 = '';

    /**
     * @GHA\Type("bool")
     * @GHA\Strategy("Zend\Hydrator\Strategy\BooleanStrategy('this_is_true', 'this_is_false')")
     * @var bool
     */
    public $prop2 = false;

    public function __construct(string $prop1 = '', bool $prop2 = false)
    {
        $this->prop1 = $prop1;
        $this->prop2 = $prop2;
    }
}
