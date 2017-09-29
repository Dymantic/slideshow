<?php


namespace Dymantic\Slideshow\Controllers;


use Dymantic\Slideshow\Slide;

class SlideOrderController extends Controller
{
    public function store()
    {
        request()->validate([
            'ordered_ids'   => 'required|array',
            'ordered_ids.*' => 'integer|exists:slides,id'
        ]);

        Slide::setOrder(request('ordered_ids'));
    }
}