<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Slide;
use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class SlidesImageTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function an_image_can_be_attached_to_a_slide()
    {
        $this->disableExceptionHandling();
        $slide = $this->createEmptySlide(['is_video' => false]);
        $this->assertCount(0, $slide->getMedia());

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/image", [
                            'image' => UploadedFile::fake()->image('testpic.jpg')
                         ]);
        $response->assertStatus(200);
        $this->assertCount(1, $slide->fresh()->getMedia(Slide::SLIDE_IMAGES));
    }

    /**
     *@test
     */
    public function the_image_is_obviously_required()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/image", []);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('image', $response);
    }

    /**
     *@test
     */
    public function the_image_must_be_a_file()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/image", [
                             'image' => 'NOT-A-FILE'
                         ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('image', $response);
    }

    /**
     *@test
     */
    public function the_image_must_be_a_valid_image_file()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/image", [
                             'image' => UploadedFile::fake()->create('not_image_file.txt')
                         ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('image', $response);
    }
}