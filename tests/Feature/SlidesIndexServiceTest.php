<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;

class SlidesIndexServiceTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function the_index_of_existing_slides_can_be_fetched()
    {
        $this->disableExceptionHandling();

        $slides = collect(range(1, 5))->map(function ($index) {
            return $this->createSlide(['slide_text' => "Slide number {$index}"]);
        });

        $response = $this->asLoggedInUser()->json('GET', "/admin/slideshow/service/slides");
        $response->assertStatus(200);

        $fetched_slides = $response->decodeResponseJson();

        $this->assertCount(5, $fetched_slides);

        $slides->each(function ($slide) use ($fetched_slides) {
            $this->assertContains($slide->toJsonableArray(), $fetched_slides);
        });

    }
}