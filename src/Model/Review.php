<?php

namespace Demo\Model;

use Faker\Generator;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Review"))
 */
class Review
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
    public $reviewer;

    /**
     * @SWG\Property()
     * @var string
     */
    public $bookId;

    /**
     * @SWG\Property()
     * @var string
     */
    public $bookTitle;

    /**
     * @SWG\Property()
     * @var string
     */
    public $review;

    /**
     * @SWG\Property(format="date")
     * @var \DateTime
     */
    public $date;

    public function fromGenerator(Generator $generator)
    {
        $this->id = $generator->uuid;
        $this->reviewer = $generator->name;
        $this->bookId = $generator->uuid;
        $this->bookTitle = substr($generator->sentence(5), 0, -1);
        $this->review = $generator->text;
        $this->date = $generator->date();
    }
}
