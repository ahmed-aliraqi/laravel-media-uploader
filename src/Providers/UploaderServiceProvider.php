<?php

namespace AhmedAliraqi\LaravelMediaUploader\Providers;

use AhmedAliraqi\LaravelMediaUploader\Console\TemporaryClearCommand;
use AhmedAliraqi\LaravelMediaUploader\Support\FFmpegDriver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        if (! defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
        }

        $this->registerConfig();

        $this->registerTranslations();

        $this->publishes([
            __DIR__.'/../Database/Migrations' => database_path('/migrations'),
        ], 'migrations');

        $this->commands([
            TemporaryClearCommand::class,
        ]);

        if (! $this->app->runningUnitTests()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('temporary:clean')->everySixHours();
            });
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->app->singleton('ffmpeg-driver', function () {
            return (new FFmpegDriver())->driver();
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/laravel-media-uploader.php' => config_path('laravel-media-uploader.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/laravel-media-uploader.php',
            'laravel-media-uploader'
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->publishes([
            __DIR__.'/../Resources/lang' => resource_path('lang/vendor/uploader'),
        ], 'uploader:translations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'uploader');
    }
}
