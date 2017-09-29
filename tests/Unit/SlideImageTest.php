<?php


namespace Dymantic\Slideshow\Tests\Unit;


use Dymantic\Slideshow\Slide;
use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\Media;

class SlideImageTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     * @group makes-images
     */
    public function an_image_can_be_set_on_a_slide()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);

        $image = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertInstanceOf(Media::class, $image);
        $this->assertEquals($image->id, $slide->fresh()->getFirstMedia(Slide::SLIDE_IMAGES)->id);
    }

    /**
     *@test
     * @group makes-images
     */
    public function setting_an_image_on_a_slide_removes_any_previous_one()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);
        $imageA = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));
        $imageB = $slide->setImage(UploadedFile::fake()->image('testimage.jpg'));

        $this->assertCount(1, $slide->fresh()->getMedia(Slide::SLIDE_IMAGES));

        $this->assertEquals($imageB->id, $slide->fresh()->getFirstMedia(Slide::SLIDE_IMAGES)->id);
    }

    /**
     *@test
     * @group makes-images
     */
    public function a_thumbnail_conversion_is_made()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);
        $image = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertNotNull($image->getUrl('thumb'));
        $this->assertFileExists($image->getPath('thumb'));
    }

    /**
     *@test
     * @group makes-images
     */
    public function a_square_conversion_is_made()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);
        $image = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertNotNull($image->getUrl('square'));
        $this->assertFileExists($image->getPath('square'));
    }

    /**
     *@test
     * @group makes-images
     */
    public function a_tall_conversion_is_made()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);
        $image = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertNotNull($image->getUrl('taller'));
        $this->assertFileExists($image->getPath('taller'));
    }

    /**
     *@test
     * @group makes-images
     */
    public function a_banner_conversion_is_made()
    {
        $slide = $this->createEmptySlide(['is_video' => false]);
        $image = $slide->setImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertNotNull($image->getUrl('banner'));
        $this->assertFileExists($image->getPath('banner'));
    }


}