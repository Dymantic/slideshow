<?php


namespace Dymantic\Slideshow\Tests\Unit;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SlideVideosTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function a_video_file_can_be_set_on_a_slide()
    {
        Storage::fake('videos');

        $slide = $this->createEmptySlide(['is_video' => true]);

        $filename = $slide->setVideo(UploadedFile::fake()->create('video.mp4'));

        $this->assertEquals($filename, $slide->fresh()->video_path);

        Storage::disk('videos')->assertExists($filename);
    }

    /**
     *@test
     */
    public function a_slide_with_a_video_can_return_the_videos_url()
    {
        $slide = $this->createEmptySlide(['is_video' => true]);
        $filename = $slide->setVideo(UploadedFile::fake()->create('video.mp4'));

        $this->assertEquals('/videos/' . $filename, $slide->fresh()->videoUrl());
    }

    /**
     *@test
     */
    public function adding_a_slide_video_removes_any_existing_ones()
    {
        Storage::fake('videos');

        $slide = $this->createEmptySlide(['is_video' => true]);

        $videoA = $slide->setVideo(UploadedFile::fake()->create('videoA.mp4'));
        $videoB = $slide->setVideo(UploadedFile::fake()->create('videoB.mp4'));

        Storage::disk('videos')->assertExists($videoB);
        Storage::disk('videos')->assertMissing($videoA);

    }

    /**
     *@test
     */
    public function deleting_a_slide_with_video_will_also_delete_the_video()
    {
        Storage::fake('videos');

        $slide = $this->createEmptySlide(['is_video' => true]);
        $video = $slide->setVideo(UploadedFile::fake()->create('videoA.mp4'));

        $slide->delete();

        Storage::disk('videos')->assertMissing($video);
    }
}