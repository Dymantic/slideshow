<?php


namespace Dymantic\Slideshow\Tests\Unit;


use Dymantic\Slideshow\Slide;
use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;

class SlideOrderingTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function the_order_of_slides_may_be_set()
    {
        $slideA = $this->createSlide();
        $slideB = $this->createSlide();
        $slideC = $this->createSlide();
        $slideD = $this->createSlide();

        Slide::setOrder([$slideB->id, $slideA->id, $slideD->id, $slideC->id]);

        $this->assertEquals(0, $slideB->fresh()->position);
        $this->assertEquals(1, $slideA->fresh()->position);
        $this->assertEquals(2, $slideD->fresh()->position);
        $this->assertEquals(3, $slideC->fresh()->position);
    }

    /**
     *@test
     */
    public function there_is_a_ordered_scope_that_returns_the_slides_in_order()
    {
        $slideA = $this->createSlide(['position' => 3]);
        $slideB = $this->createSlide(['position' => 0]);
        $slideC = $this->createSlide(['position' => 2]);
        $slideD = $this->createSlide(['position' => 1]);

        $slides = Slide::ordered()->get();

        $this->assertTrue($slides[0]->is($slideB));
        $this->assertTrue($slides[1]->is($slideD));
        $this->assertTrue($slides[2]->is($slideC));
        $this->assertTrue($slides[3]->is($slideA));
    }
}