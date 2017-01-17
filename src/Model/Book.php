<?php

namespace Demo\Model;

use Faker\Generator;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Book"))
 */
class Book
{
    /**
     * @SWG\Property()
     * @var string
     */
    public $id;

    /**
     * @SWG\Property()
     * @var string
     */
    public $author;

    /**
     * @SWG\Property()
     * @var string
     */
    public $title;

    /**
     * @SWG\Property()
     * @var string
     */
    public $summary;

    /**
     * @SWG\Property()
     * @var int
     */
    public $price;

    /**
     * @SWG\Property()
     * @var string
     */
    public $isbn;

    public function fromGenerator(Generator $generator)
    {
        $this->id = $generator->uuid;
        $this->author = $generator->name;
        $this->title = substr($generator->sentence(5), 0, -1);
        $this->summary = $generator->text;
        $this->price = $generator->randomNumber(2);
        $this->isbn = $generator->ean13;
    }
}
