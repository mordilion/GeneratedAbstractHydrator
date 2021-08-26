<?php

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\Tests\ExampleClasses;

class Author
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var Book[]
     */
    public $books;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }
}
