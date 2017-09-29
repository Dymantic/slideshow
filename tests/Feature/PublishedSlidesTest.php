<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;

class PublishedSlidesTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function a_slide_may_be_published()
    {
        $this->disableExceptionHandling();
        $slide = $this->createSlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/published-slides", [
                             'slide_id' => $slide->id
                         ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('slides', [
            'id'        => $slide->id,
            'published' => true
        ]);
    }

    /**
     *@test
     */
    public function a_published_slide_can_be_retracted()
    {
        $this->disableExceptionHandling();
        $slide = $this->createSlide();

        $response = $this->asLoggedInUser()
                         ->json('DELETE', "/admin/slideshow/published-slides/{$slide->id}");
        $response->assertStatus(200);

        $this->assertDatabaseHas('slides', [
            'id'        => $slide->id,
            'published' => false
        ]);
    }
}