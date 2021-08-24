# GeneratedAbstractHydrator
## Installation
`composer require mordilion/generated-abstract-hydrator`

## Examples
### Simple
#### Generator-Method
```php
use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Mordilion\GeneratedAbstractHydrator\Hydrator\PerformantAbstractHydrator;

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
```
#### Usage of nested Objects
```php
use Mordilion\GeneratedAbstractHydrator\Strategy\RecursiveHydrationStrategy;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

class Book
{
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var DateTime
     */
    public $publishedAt;
}

class Author
{
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $firstname;
    
    /**
     * @var Book[]
     */
    public $books;
}

// ---

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
```

output:
```
class Author#2 (3) {
  public $name =>
  string(5) "Böll"
  public $firstname =>
  string(8) "Heinrich"
  public $books =>
  array(3) {
    [0] =>
    class Book#4 (2) {
      public $title =>
      string(20) "Die schwarzen Schafe"
      public $publishedAt =>
      class DateTime#3841 (3) {
        public $date =>
        string(26) "1951-01-01 06:17:41.000000"
        public $timezone_type =>
        int(3)
        public $timezone =>
        string(3) "UTC"
      }
    }
    [1] =>
    class Book#3840 (2) {
      public $title =>
      string(18) "Wo warst du, Adam?"
      public $publishedAt =>
      class DateTime#3839 (3) {
        public $date =>
        string(26) "1951-01-01 06:17:41.000000"
        public $timezone_type =>
        int(3)
        public $timezone =>
        string(3) "UTC"
      }
    }
    [2] =>
    class Book#3838 (2) {
      public $title =>
      string(22) "Ansichten eines Clowns"
      public $publishedAt =>
      class DateTime#3837 (3) {
        public $date =>
        string(26) "1963-01-01 06:17:41.000000"
        public $timezone_type =>
        int(3)
        public $timezone =>
        string(3) "UTC"
      }
    }
  }
}
```
