<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;

class DeletingSlideTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function a_slide_can_be_deleted()
    {
        $this->disableExceptionHandling();
        $slide = $this->createSlide();

        $response = $this->asLoggedInUser()->json('DELETE', "/admin/slideshow/slides/{$slide->id}");
        $response->assertStatus(302);
        $response->assertRedirect('/admin/slideshow/slides');

        $this->assertDatabaseMissing('slides', ['id' => $slide->id]);
    }
}