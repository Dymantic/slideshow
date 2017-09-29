<?php


namespace Dymantic\Slideshow;


class SlideObserver
{
    public function deleting(Slide $slide)
    {
        if($slide->hasVideo()) {
            $slide->clearExistingVideo();
        }
    }
}