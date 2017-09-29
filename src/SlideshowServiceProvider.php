<?php


namespace Dymantic\Slideshow;


use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class SlideshowServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        Slide::observe(new SlideObserver());
    }

    public function register()
    {
        $this->app->register(MediaLibraryServiceProvider::class);
    }
}