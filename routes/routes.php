<?php

Route::group([
    'prefix'     => 'admin/slideshow',
    'namespace'  => 'Dymantic\Slideshow\Controllers',
    'middleware' => ['web', 'auth', 'bindings']
],
    function () {

        Route::post('slides', 'SlidesController@store');
        Route::post('slides/{slide}', 'SlidesController@update');
        Route::delete('slides/{slide}', 'SlidesController@delete');

        Route::post('slides/{slide}/image', 'SlideImageController@store');

        Route::post('published-slides', 'PublishedSlidesController@store');
        Route::delete('published-slides/{slide}', 'PublishedSlidesController@delete');

        Route::post('slides/{slide}/video', 'SlideVideoController@store');

        Route::post('slide-order', 'SlideOrderController@store');

        Route::get('service/slides', 'SlidesServiceController@index');

    });