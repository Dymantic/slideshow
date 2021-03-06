<?php

namespace Dymantic\Slideshow\Tests;

use Dymantic\Slideshow\SlideshowServiceProvider;
use File;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Exceptions\Handler;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{

    public function setUp() : void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    public function asLoggedInUser()
    {
        $this->actingAs(TestUserModel::first());

        return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}

            public function report(\Throwable $e)
            {
                // no-op
            }

            public function render($request, \Throwable $e) {
                throw $e;
            }

        });
    }

    public function assertJsonValidationErrorExists($field, $response)
    {
        return $this->assertArrayHasKey($field, $response->decodeResponseJson()['errors']);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SlideshowServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory(__DIR__ . '/temp');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('filesystems.disks.media', [
            'driver' => 'local',
            'root'   => __DIR__ . '/temp/media',
        ]);

        $app['config']->set('filesystems.disks.videos', [
            'driver' => 'local',
            'root'   => __DIR__ . '/temp/videos',
        ]);

        $app['config']->set('medialibrary', [
            'disk_name' => 'media',
            'max_file_size' => 1024 * 1024 * 10,
            'queue_name' => '',
            'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),
            'media_model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,
            'remote' => [
                'extra_headers' => [
                    'CacheControl' => 'max-age=604800',
                ],
            ],
            'responsive_images' => [
                'use_tiny_placeholders' => true,
                'tiny_placeholder_generator' => \Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
            ],
            'default_loading_attribute_value' => null,
            'conversion_file_namer' => \Spatie\MediaLibrary\Conversions\DefaultConversionFileNamer::class,
            'path_generator' => \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,
            'url_generator' => \Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,
            'version_urls' => false,
            'image_optimizers' => [
                \Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
                    '--strip-all',
                    '--all-progressive',
                ],
                \Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
                    '--force', // required parameter for this package
                ],
                \Spatie\ImageOptimizer\Optimizers\Optipng::class => [
                    '-i0', // this will result in a non-interlaced, progressive scanned image
                    '-o2', // this set the optimization level to two (multiple IDAT compression trials)
                    '-quiet', // required parameter for this package
                ],
                \Spatie\ImageOptimizer\Optimizers\Svgo::class => [
                    '--disable=cleanupIDs', // disabling because it is known to cause troubles
                ],
                \Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
                    '-b', // required parameter for this package
                    '-O3', // this produces the slowest but best results
                ],
            ],
            'image_generators' => [
                \Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
                \Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
                \Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
                \Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
                \Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
            ],
            'image_driver' => env('IMAGE_DRIVER', 'gd'),
            'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
            'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
            'temporary_directory_path' => null,
            'jobs' => [
                'perform_conversions' => \Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
                'generate_responsive_images' => \Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
            ],
            'media_downloader' => \Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,
        ]);

        $app->bind('path.public', function () {
            return __DIR__ . '/temp';
        });

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
        });
//
//        $app['db']->connection()->getSchemaBuilder()->create('nameless_authors', function (Blueprint $table) {
//            $table->increments('id');
//        });

        $app['db']->connection()->getSchemaBuilder()->create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->nullableTimestamps();
        });

        TestUserModel::create(['name' => 'test user', 'email' => 'test@example.com', 'password' => 'password']);

        include_once __DIR__ . '/../database/migrations/create_slides_table.php.stub';

        (new \CreateSlidesTable())->up();
    }

    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
    }
}
