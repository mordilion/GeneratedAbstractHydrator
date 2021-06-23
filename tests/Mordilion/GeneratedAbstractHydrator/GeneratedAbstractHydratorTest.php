<?php

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\Tests;

use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;
use Mordilion\GeneratedAbstractHydrator\Strategy\RecursiveHydrationStrategy;
use Mordilion\GeneratedAbstractHydrator\Tests\ExampleClasses\Author;
use Mordilion\GeneratedAbstractHydrator\Tests\ExampleClasses\Book;
use PHPUnit\Framework\TestCase;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

class GeneratedAbstractHydratorTest extends TestCase
{

    public function test_common()
    {
        $data = [
            'name' => 'Böll',
            'first_name' => 'Heinrich',
            'books' => [
                ['title' => 'Die schwarzen Schafe', 'publishedAt' => '1951-01-01'],
                ['title' => 'Wo warst du, Adam?', 'publishedAt' => '1951-01-01'],
                ['title' => 'Ansichten eines Clowns', 'publishedAt' => '1963-01-01'],
            ],
        ];

        $bookHydrator = $this->getClassHydrator(Book::class);
        $bookHydrator->addStrategy('publishedAt', new DateTimeFormatterStrategy('Y-m-d'));

        $authorHydrator = $this->getClassHydrator(Author::class);
        $authorHydrator->addStrategy('books', new RecursiveHydrationStrategy(new Book(), $bookHydrator, true));

        $object = new Author();
        $authorHydrator->hydrate($data, $object);

        var_dump($object);
    }

    function getClassHydrator(string $class): PerformantAbstractHydrator
    {
        $config = new Configuration($class);
        $config->setHydratorGenerator(new AbstractHydratorGenerator(PerformantAbstractHydrator::class));
        $hydratorClass = $config->createFactory()->getHydratorClass();

        if (!class_exists($hydratorClass)) {
            throw new \RuntimeException('Could not create Hydrator!');
        }

        /** @var PerformantAbstractHydrator $hydrator */
        $hydrator = new $hydratorClass();

        return $hydrator;
    }
}
