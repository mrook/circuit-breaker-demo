<?php

namespace Demo\Model;

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
}
