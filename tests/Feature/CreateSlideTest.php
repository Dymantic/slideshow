<?php

namespace Dymantic\Slideshow\Tests\Feature;

use Dymantic\Slideshow\Slide;
use Dymantic\Slideshow\Tests\TestCase;

class CreateSlideTest extends TestCase
{
    /**
     * @test
     */
    public function an_image_slide_can_be_created()
    {
        $this->disableExceptionHandling();
        $response = $this->asLoggedInUser()->post('/admin/slideshow/slides', ['video_slide' => false]);
        $response->assertStatus(302);

        $this->assertCount(1, Slide::all());
        $slide_id = Slide::first()->id;
        $response->assertRedirect("/admin/slideshow/slides/{$slide_id}");

        $this->assertDatabaseHas('slides', [
            'id'          => $slide_id,
            'is_video'    => false,
            'video_path'  => null,
            'slide_text'  => null,
            'action_text' => null,
            'action_link' => null,
            'text_colour' => null
        ]);
    }

    /**
     *@test
     */
    public function a_video_slide_can_be_created()
    {
        $this->disableExceptionHandling();
        $response = $this->asLoggedInUser()->post('/admin/slideshow/slides', ['video_slide' => true]);
        $response->assertStatus(302);

        $this->assertCount(1, Slide::all());
        $slide_id = Slide::first()->id;
        $response->assertRedirect("/admin/slideshow/slides/{$slide_id}");

        $this->assertDatabaseHas('slides', [
            'id'          => $slide_id,
            'is_video'    => true,
            'video_path'  => null,
            'slide_text'  => null,
            'action_text' => null,
            'action_link' => null,
            'text_colour' => null
        ]);
    }

    /**
     *@test
     */
    public function creating_a_slide_requires_the_video_slide_flag()
    {
        $response = $this->asLoggedInUser()->post('/admin/slideshow/slides', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('video_slide');

        $this->assertCount(0, Slide::all());
    }

    /**
     *@test
     */
    public function the_video_slide_flag_must_be_a_boolean()
    {
        $response = $this->asLoggedInUser()->post('/admin/slideshow/slides', ['video_slide' => "I AIN'T NO BOOL"]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('video_slide');

        $this->assertCount(0, Slide::all());
    }
}