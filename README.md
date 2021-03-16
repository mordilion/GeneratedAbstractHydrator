# GeneratedAbstractHydrator
## Installation
`compsoer require mordilion/generated-abstract-hydrator`

## Examples
### Simple
#### Generator-Method
```php
use GeneratedHydrator\Configuration;
use Mordilion\GeneratedAbstractHydrator\ClassGenerator\AbstractHydratorGenerator;
use Zend\Hydrator\AbstractHydrator;

function getClassHydrator(string $class): AbstractHydrator
{
    $config = new Configuration($class);
    $config->setHydratorGenerator(new AbstractHydratorGenerator());
    $hydratorClass = $config->createFactory()->getHydratorClass();

    if (!class_exists($hydratorClass)) {
        throw new \RuntimeException('Could not create Hydrator!');
    }

    /** @var AbstractHydrator $hydrator */
    $hydrator = new $hydratorClass();

    return $hydrator;
}
```
#### Annotations
```php
use Mordilion\GeneratedAbstractHydrator\Annotation as GHA;

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
```

