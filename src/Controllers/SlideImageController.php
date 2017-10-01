<?php


namespace Dymantic\Slideshow\Controllers;


use Dymantic\Slideshow\Slide;

class SlideImageController extends Controller
{
    public function store(Slide $slide)
    {
        request()->validate(['image' => 'required|image']);

        $slide->setImage(request('image'));

        return ['url' => $slide->fresh()->imageUrl('banner')];
    }
}