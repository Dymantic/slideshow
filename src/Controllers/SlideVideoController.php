<?php


namespace Dymantic\Slideshow\Controllers;


use Dymantic\Slideshow\Slide;

class SlideVideoController extends Controller
{
    public function store(Slide $slide)
    {
        request()->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/webm|max:12000'
        ]);

        $path = $slide->setVideo(request('video'));

        return ['location' => $path, 'url' => $slide->fresh()->videoUrl()];
    }
}