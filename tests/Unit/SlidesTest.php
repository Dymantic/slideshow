<?php

namespace Dymantic\Slideshow\Tests\Unit;

use Dymantic\Slideshow\Slide;
use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class SlidesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_empty_image_slide_can_be_created()
    {
        $slide = Slide::createImageSlide();

        $this->assertFalse($slide->is_video);
        $this->assertNull($slide->video_path);
        $this->assertNull($slide->slide_text);
        $this->assertNull($slide->action_text);
        $this->assertNull($slide->action_link);
        $this->assertNull($slide->text_colour);

    }

    /**
     *@test
     */
    public function an_empty_video_slide_can_be_created()
    {
        $slide = Slide::createVideoSlide();

        $this->assertTrue($slide->is_video);
        $this->assertNull($slide->video_path);
        $this->assertNull($slide->slide_text);
        $this->assertNull($slide->action_text);
        $this->assertNull($slide->action_link);
        $this->assertNull($slide->text_colour);
    }

    /**
     *@test
     */
    public function a_slide_can_be_published()
    {
        $slide = $this->createSlide(['published' => false]);

        $slide->publish();

        $this->assertTrue($slide->fresh()->published);
    }

    /**
     *@test
     */
    public function a_slide_can_be_retracted()
    {
        $slide = $this->createSlide(['published' => true]);

        $slide->retract();

        $this->assertFalse($slide->fresh()->published);
    }

    /**
     *@test
     */
    public function a_slide_can_be_presented_as_a_jsonable_array()
    {
        $slide = $this->createSlide([
            'is_video' => true,
            'slide_text' => 'TEST SLIDE TEXT',
            'action_text' => 'TEST ACTION TEXT',
            'action_link' => 'TEST ACTION LINK',
            'text_colour' => 'TEST COLOUR',
            'position' => 3
        ]);
        $filename = $slide->setVideo(UploadedFile::fake()->create('video.mp4'));

        $expected = [
            'is_video' => true,
            'video_path' => $filename,
            'slide_text' => 'TEST SLIDE TEXT',
            'action_text' => 'TEST ACTION TEXT',
            'action_link' => 'TEST ACTION LINK',
            'text_colour' => 'TEST COLOUR',
            'has_image' => false,
            'thumb_image' => null,
            'square_image' => null,
            'taller_image' => null,
            'banner_image' => null,
            'has_video' => true,
            'video_url' => '/videos/' . $filename
        ];

        $this->assertEquals($expected, $slide->toJsonableArray());
    }

    /**
     *@test
     */
    public function a_slide_knows_if_it_has_its_own_image()
    {
        $slideA = $this->createSlide();
        $slideB = $this->createSlide();
        $slideB->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertFalse($slideA->hasImage());
        $this->assertTrue($slideB->hasImage());
    }

    /**
     *@test
     */
    public function the_url_of_the_slides_image_can_be_queried()
    {
        $slide = $this->createSlide();
        $image = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertEquals($image->getUrl('thumb'), $slide->fresh()->imageUrl('thumb'));
        $this->assertEquals($image->getUrl('square'), $slide->fresh()->imageUrl('square'));
        $this->assertEquals($image->getUrl('taller'), $slide->fresh()->imageUrl('taller'));
        $this->assertEquals($image->getUrl('banner'), $slide->fresh()->imageUrl('banner'));
    }

    /**
     *@test
     */
    public function image_urls_are_null_for_non_existing_images()
    {
        $slide = $this->createSlide();

        $this->assertNull($slide->fresh()->imageUrl('thumb'));
        $this->assertNull($slide->fresh()->imageUrl('square'));
        $this->assertNull($slide->fresh()->imageUrl('taller'));
        $this->assertNull($slide->fresh()->imageUrl('banner'));
    }

    /**
     *@test
     */
    public function a_slide_knows_if_it_has_a_video()
    {
        $slideA = $this->createSlide(['is_video' => true]);
        $slideB = $this->createSlide(['is_video' => true]);
        $slideB->setVideo(UploadedFile::fake()->create('video.mp4'));

        $this->assertFalse($slideA->hasVideo());
        $this->assertTrue($slideB->hasVideo());
    }

    /**
     *@test
     * @group makes-images
     */
    public function a_slide_is_usable_if_it_has_either_an_image_or_video_and_is_also_published()
    {
        $slideA = $this->createSlide(['is_video' => true, 'published' => true]);
        $slideB = $this->createSlide(['is_video' => false, 'published' => true]);
        $slideC = $this->createSlide(['is_video' => true, 'published' => false]);
        $slideD = $this->createSlide(['is_video' => false, 'published' => true]);

        $slideA->setVideo(UploadedFile::fake()->create('video.mp4'));
        $slideC->setVideo(UploadedFile::fake()->create('video2.mp4'));
        $slideB->setImage(UploadedFile::fake()->image('testpic.png'));

        $this->assertTrue($slideA->isUsable());
        $this->assertTrue($slideB->isUsable());
        $this->assertFalse($slideC->isUsable());
        $this->assertFalse($slideD->isUsable());
    }

}