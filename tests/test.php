<?php

declare(strict_types=1);

ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '256');
ini_set('xdebug.var_display_max_data', '1024');

require __DIR__ . '/../vendor/autoload.php';

use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\Annotation as GHA;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Zend\Hydrator\HydratorInterface;

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
    $config->setHydratorGenerator(new AbstractHydratorGenerator());
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
    'first_name' => 'Heinrich',
    'books' => [
        ['title' => 'Die schwarzen Schafe', 'publishedAt' => '1951-01-01'],
        ['title' => 'Wo warst du, Adam?', 'publishedAt' => '1951-01-01'],
        ['title' => 'Ansichten eines Clowns', 'publishedAt' => '1963-01-01'],
    ],
];

$hydrator = getClassHydrator(Author::class);

$object = new Author();
$hydrator->hydrate($data, $object);

var_dump($object);

