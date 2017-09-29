<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoSlidesTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function a_video_file_can_be_attached_to_a_slide()
    {
        Storage::fake('videos');

        $this->disableExceptionHandling();
        $slide = $this->createEmptySlide(['is_video' => true]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/video", [
                             'video' => UploadedFile::fake()->create('video.mp4')
                         ]);
        $response->assertStatus(200);

        Storage::disk('videos')->assertExists($response->decodeResponseJson()['location']);

        $this->assertDatabaseHas('slides', [
            'id'         => $slide->id,
            'is_video'   => true,
            'video_path' => $response->decodeResponseJson()['location']
        ]);

        $this->assertEquals('/videos/' . $slide->fresh()->video_path, $response->decodeResponseJson()['url']);
    }

    /**
     *@test
     */
    public function the_video_field_is_required()
    {
        $slide = $this->createEmptySlide(['is_video' => true]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/video", []);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('video', $response);
    }

    /**
     *@test
     */
    public function the_video_must_be_a_file()
    {
        $slide = $this->createEmptySlide(['is_video' => true]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/video", [
                             'video' => 'NOT_A_FILE'
                         ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('video', $response);
    }

    /**
     *@test
     */
    public function the_video_must_have_a_valid_mime_type()
    {
        $slide = $this->createEmptySlide(['is_video' => true]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/video", [
                             'video' => UploadedFile::fake()->image('testpic.png')
                         ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('video', $response);
    }

    /**
     *@test
     */
    public function an_mp4_file_is_acceptable()
    {
        $slide = $this->createEmptySlide(['is_video' => true]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/video", [
                             'video' => UploadedFile::fake()->create('video.mp4')
                         ]);
        $response->assertStatus(200);
    }

    /**
     *@test
     */
    public function the_file_may_not_exceed_twelve_mb_in_size()
    {
        $slide = $this->createEmptySlide(['is_video' => true]);

        $response = $this->asLoggedInUser()
                         ->json('POST', "/admin/slideshow/slides/{$slide->id}/video", [
                             'video' => UploadedFile::fake()->create('video.mp4', 13 * 1024)
                         ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('video', $response);
    }
}