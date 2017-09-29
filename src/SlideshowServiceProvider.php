<?php


namespace Dymantic\Slideshow;


use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class SlideshowServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if (!class_exists('CreateSlidesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_slides_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_slides_table.php'),
            ], 'migrations');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        Slide::observe(new SlideObserver());
    }

    public function register()
    {
        $this->app->register(MediaLibraryServiceProvider::class);
    }
}
