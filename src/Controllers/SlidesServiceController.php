<?php


namespace Dymantic\Slideshow\Controllers;


use Dymantic\Slideshow\Slide;

class SlidesServiceController extends Controller
{
    public function index()
    {
        return Slide::latest()->get()->map->toJsonableArray();
    }
}