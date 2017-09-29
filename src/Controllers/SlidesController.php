<?php

namespace Dymantic\Slideshow\Controllers;

use Dymantic\Slideshow\Slide;

class SlidesController extends Controller
{
    public function store()
    {
        request()->validate(['video_slide' => 'boolean']);

        $slide = request('video_slide', false) ? Slide::createVideoSlide() : Slide::createImageSlide();

        return redirect("/admin/slideshow/slides/{$slide->id}");
    }

    public function update(Slide $slide)
    {
        request()->validate([
            'slide_text' => 'max:60',
            'action_text' => 'max:16'
        ]);
        $slide->update(request()->all([
            'slide_text',
            'action_text',
            'action_link',
            'text_colour'
        ]));

        return $slide->fresh()->toJsonableArray();
    }

    /**
     *@test
     */
    public function delete(Slide $slide)
    {
        $slide->delete();
    }
}