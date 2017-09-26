<?php


namespace Dymantic\Slideshow\Tests;


class ExampleTest extends TestCase
{
    /**
     *@test
     */
    public function it_is_true()
    {
        $this->asLoggedInUser()->assertTrue(true);
    }
}