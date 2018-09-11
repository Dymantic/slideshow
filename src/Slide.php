<?php


namespace Dymantic\Slideshow;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Slide extends Model implements HasMedia
{
    use HasMediaTrait;

    const SLIDE_IMAGES = 'slide-image';

    protected $table = 'slides';

    protected $casts = [
        'published' => 'boolean',
        'position'  => 'integer'
    ];

    protected $fillable = [
        'is_video',
        'slide_text',
        'action_text',
        'action_link',
        'text_colour',
        'position'
    ];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
             ->fit(Manipulations::FIT_CROP, 210, 70)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::SLIDE_IMAGES)
             ->optimize();

        $this->addMediaConversion('square')
             ->fit(Manipulations::FIT_CROP, 500, 500)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::SLIDE_IMAGES)
             ->optimize();

        $this->addMediaConversion('taller')
             ->fit(Manipulations::FIT_CROP, 960, 640)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::SLIDE_IMAGES)
             ->optimize();

        $this->addMediaConversion('banner')
             ->fit(Manipulations::FIT_CROP, 1400, 560)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::SLIDE_IMAGES)
             ->optimize();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    public static function createImageSlide()
    {
        return static::create([
            'is_video' => false
        ]);
    }

    public static function createVideoSlide()
    {
        return static::create([
            'is_video' => true
        ]);
    }

    public function setImage($file)
    {
        $this->clearMediaCollection(static::SLIDE_IMAGES);

        return $this->addMedia($file)->preservingOriginal()->toMediaCollection(static::SLIDE_IMAGES);
    }

    public function hasImage()
    {
        return $this->hasMedia(static::SLIDE_IMAGES);
    }

    public function imageUrl($conversion = '')
    {
        $image = $this->getFirstMedia(static::SLIDE_IMAGES);

        return $image ? $image->getUrl($conversion) : null;
    }

    public function publish()
    {
        $this->published = true;

        return $this->save();
    }

    public function retract()
    {
        $this->published = false;

        return $this->save();
    }

    public function setVideo(UploadedFile $file)
    {
        if ($this->hasVideo()) {
            $this->clearExistingVideo();
        }
        $filename = $file->store('', 'videos');
        $this->video_path = $filename;
        $this->save();

        return $filename;
    }

    public function clearExistingVideo()
    {
        Storage::disk('videos')->delete($this->video_path);
    }

    public function videoUrl()
    {
        return '/videos/' . $this->video_path;
    }

    public function hasVideo()
    {
        return $this->is_video && $this->video_path && Storage::disk('videos')->exists($this->video_path);
    }

    public static function setOrder($ids)
    {
        collect($ids)->each(function ($id, $position) {
            static::findOrFail($id)->update(['position' => $position]);
        });
    }

    public function toJsonableArray()
    {
        return [
            'id'           => $this->id,
            'slide_type'   => $this->is_video ? 'video' : 'image',
            'is_video'     => $this->is_video,
            'video_path'   => $this->video_path,
            'slide_text'   => $this->slide_text,
            'action_text'  => $this->action_text,
            'action_link'  => $this->action_link,
            'text_colour'  => $this->text_colour,
            'has_image'    => $this->hasImage(),
            'thumb_image'  => $this->imageUrl('thumb'),
            'square_image' => $this->imageUrl('square'),
            'taller_image' => $this->imageUrl('taller'),
            'banner_image' => $this->imageUrl('banner'),
            'has_video'    => $this->hasVideo(),
            'video_url'    => $this->videoUrl(),
            'published'    => $this->published
        ];
    }

    public function isUsable()
    {
        return ($this->hasImage() || $this->hasVideo()) && $this->published;
    }

}