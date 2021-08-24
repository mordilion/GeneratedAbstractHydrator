<?php

declare(strict_types=1);

ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '256');
ini_set('xdebug.var_display_max_data', '1024');

require __DIR__ . '/../vendor/autoload.php';

use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;
use Mordilion\GeneratedAbstractHydrator\Strategy\RecursiveHydrationStrategy;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

class Book
{
    /**
     * @GHA\Type("string")
     * @var string
     */
    public $title;

    /**
     * @GHA\Type("DateTime<'Y-m-d'>")
     * @var DateTime
     */
    public $publishedAt;
}

class Author
{
    /**
     * @GHA\Type("string")
     * @var string
     */
    public $name;

    /**
     * @GHA\Type("string")
     * @GHA\SerializedName("first_name")
     * @var string
     */
    public $firstname;

    /**
     * @GHA\Type("array<Book>")
     * @var Book[]
     */
    public $books;
}

function getClassHydrator(string $class): HydratorInterface
{
    $config = new Configuration($class);
    $config->setHydratorGenerator(new AbstractHydratorGenerator(PerformantAbstractHydrator::class));
    $hydratorClass = $config->createFactory()->getHydratorClass();

    if (!class_exists($hydratorClass)) {
        throw new \RuntimeException('Could not create Hydrator!');
    }

    /** @var HydratorInterface $hydrator */
    $hydrator = new $hydratorClass();

    return $hydrator;
}

$data = [
    'name' => 'Böll',
    'firstname' => 'Heinrich',
    'books' => [
        ['title' => 'Die schwarzen Schafe', 'publishedAt' => '1951-01-01'],
        ['title' => 'Wo warst du, Adam?', 'publishedAt' => '1951-01-01'],
        ['title' => 'Ansichten eines Clowns', 'publishedAt' => '1963-01-01'],
    ],
];

$bookHydrator = getClassHydrator(Book::class);
$bookHydrator->addStrategy('publishedAt', new DateTimeFormatterStrategy('Y-m-d'));

$authorHydrator = getClassHydrator(Author::class);
$authorHydrator->addStrategy('books', new RecursiveHydrationStrategy(new Book(), $bookHydrator, true));

$object = new Author();
$authorHydrator->hydrate($data, $object);

var_dump($object);

