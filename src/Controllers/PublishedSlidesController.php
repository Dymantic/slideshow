<?php


namespace Dymantic\Slideshow\Controllers;


use Dymantic\Slideshow\Slide;

class PublishedSlidesController extends Controller
{
    public function store()
    {
        Slide::findOrFail(request('slide_id'))->publish();
    }

    public function delete(Slide $slide)
    {
        $slide->retract();
    }
}