<?php


namespace Dymantic\Slideshow\Tests;


use Dymantic\Slideshow\Slide;

trait MakesModels
{
    public function createSlide($attributes = []) : Slide
    {
        $defaults = [
            'is_video'    => false,
            'video_path'  => null,
            'slide_text'  => 'TEST SLIDE TEXT',
            'action_text' => 'TEST ACTION TEXT',
            'action_link' => 'TEST ACTION LINK',
            'text_colour' => 'TEST COLOUR'
        ];

        return Slide::forceCreate(array_merge($defaults, $attributes));
    }

    public function createEmptySlide($attributes = []) : Slide
    {
        $defaults = [
            'is_video'    => false,
            'video_path'  => null,
            'slide_text'  => null,
            'action_text' => null,
            'action_link' => null,
            'text_colour' => null
        ];

        return Slide::forceCreate(array_merge($defaults, $attributes));
    }
}