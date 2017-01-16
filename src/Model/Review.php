<?php

namespace Demo\Model;

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
}
