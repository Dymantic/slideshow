<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;

class UpdateSlideTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function an_existing_slide_can_be_updated()
    {
        $this->disableExceptionHandling();
        $slide = $this->createEmptySlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}", [
                             'slide_text'  => 'NEW SLIDE TEXT',
                             'action_text' => 'NEW ACTION TEXT',
                             'action_link' => 'http://example.com',
                             'text_colour' => 'NEW ACTION COLOUR'
                         ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('slides', [
            'id' => $slide->id,
            'slide_text'  => 'NEW SLIDE TEXT',
            'action_text' => 'NEW ACTION TEXT',
            'action_link' => 'http://example.com',
            'text_colour' => 'NEW ACTION COLOUR'
        ]);
    }

    /**
     *@test
     */
    public function successfully_updating_a_slide_returns_the_updated_data()
    {
        $this->disableExceptionHandling();
        $slide = $this->createEmptySlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}", [
                             'slide_text'  => 'NEW SLIDE TEXT',
                             'action_text' => 'NEW ACTION TEXT',
                             'action_link' => 'http://example.com',
                             'text_colour' => 'NEW ACTION COLOUR'
                         ]);
        $response->assertStatus(200);

        $this->assertEquals($slide->fresh()->toJsonableArray(), $response->json());
    }
    
    /**
     *@test
     */
    public function the_slide_text_cannot_be_longer_than_60_characters()
    {
        $slide = $this->createEmptySlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}", [
                             'slide_text'  => str_repeat('X', 61)
                         ]);
        $response->assertStatus(422);
        $this->assertArrayHasKey('slide_text', $response->decodeResponseJson()['errors']);
    }

    /**
     *@test
     */
    public function the_action_text_cannot_be_longer_than_16_characters()
    {
        $slide = $this->createEmptySlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}", [
                             'action_text'  => str_repeat('X', 17)
                         ]);
        $response->assertStatus(422);
        $this->assertArrayHasKey('action_text', $response->decodeResponseJson()['errors']);
    }

    /**
     *@test
     */
    public function the_action_link_must_be_a_valid_url()
    {
        $slide = $this->createEmptySlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}", [
                             'action_link'  => 'NOT-A-VALID-URL'
                         ]);
        $response->assertStatus(422);
        $this->assertArrayHasKey('action_link', $response->decodeResponseJson()['errors']);
    }

    /**
     *@test
     */
    public function the_action_url_may_be_empty()
    {
        $slide = $this->createEmptySlide();

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}", [
                             'action_link'  => '',
                             'slide_text' => 'TEST SLIDE TEXT'
                         ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('slides', [
            'id' => $slide->id,
            'action_link' => null,
            'slide_text' => 'TEST SLIDE TEXT'
        ]);
    }
}