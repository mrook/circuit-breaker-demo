<?php

use Behat\MinkExtension\Context\RawMinkContext;

class FeatureContext extends RawMinkContext
{
    /**
     * @var string|null $name
     */
    private $name = null;

    /**
     * @Given there are books
     */
    public function thereAreBooks()
    {
    }

    /**
     * @Given there are reviews
     */
    public function thereAreReviews()
    {
    }

    /**
     * @When I look at the overview
     */
    public function iLookAtTheOverview()
    {
        $this->getSession()->visit($this->locatePath('/'));
    }

    /**
     * @Then I should see the last book
     */
    public function iShouldSeeTheLastBook()
    {
        $this->assertSession()->pageTextContains("Our last book");
    }

    /**
     * @Then I should see the last review
     */
    public function iShouldSeeTheLastReview()
    {
        $this->assertSession()->pageTextContains("The latest review");
    }
}
