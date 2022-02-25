<?php

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\Tests;

use DateTime;
use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;
use Mordilion\GeneratedAbstractHydrator\Strategy\RecursiveHydrationStrategy;
use Mordilion\GeneratedAbstractHydrator\Tests\ExampleClasses\Author;
use Mordilion\GeneratedAbstractHydrator\Tests\ExampleClasses\Book;
use PHPUnit\Framework\TestCase;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class GeneratedAbstractHydratorTest extends TestCase
{
    public function setUp(): void
    {
        foreach (glob(__DIR__ . '/GeneratedClasses/*.php') as $file) {
            unlink($file);
        }
    }

    public function testCommon()
    {
        $data = [
            'name' => 'Böll',
            'first_name' => 'Heinrich',
            'books' => [
                ['title' => 'Die schwarzen Schafe', 'published_at' => '1951-01-01'],
                ['title' => 'Wo warst du, Adam?', 'published_at' => '1951-01-01'],
                ['title' => 'Ansichten eines Clowns', 'published_at' => '1963-01-01'],
            ],
        ];

        $bookHydrator = $this->getClassHydrator(Book::class);
        $bookHydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        $bookHydrator->addStrategy('publishedAt', new DateTimeFormatterStrategy('Y-m-d'));

        $authorHydrator = $this->getClassHydrator(Author::class);
        $authorHydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        $authorHydrator->addStrategy('books', new RecursiveHydrationStrategy(new Book(), $bookHydrator, true));

        $object = new Author();
        $authorHydrator->hydrate($data, $object);

        self::assertEquals($data['name'], $object->getName());
        self::assertEquals($data['first_name'], $object->getFirstName());

        self::assertEquals($data['books'][0]['title'], $object->books[0]->title);
        self::assertInstanceOf(DateTime::class, $object->books[0]->publishedAt);
        self::assertEquals($data['books'][0]['published_at'], $object->books[0]->publishedAt->format('Y-m-d'));

        self::assertEquals($data['books'][1]['title'], $object->books[1]->title);
        self::assertInstanceOf(DateTime::class, $object->books[1]->publishedAt);
        self::assertEquals($data['books'][1]['published_at'], $object->books[1]->publishedAt->format('Y-m-d'));

        self::assertEquals($data['books'][2]['title'], $object->books[2]->title);
        self::assertInstanceOf(DateTime::class, $object->books[2]->publishedAt);
        self::assertEquals($data['books'][2]['published_at'], $object->books[2]->publishedAt->format('Y-m-d'));
    }

    private function getClassHydrator(string $class): PerformantAbstractHydrator
    {
        $config = new Configuration($class);
        $config->setHydratorGenerator(new AbstractHydratorGenerator(PerformantAbstractHydrator::class));
        $config->setGeneratedClassesTargetDir(__DIR__ . '/GeneratedClasses');
        $hydratorClass = $config->createFactory()->getHydratorClass();

        if (!class_exists($hydratorClass)) {
            throw new \RuntimeException('Could not create Hydrator!');
        }

        /** @var PerformantAbstractHydrator $hydrator */
        $hydrator = new $hydratorClass();

        return $hydrator;
    }
}
