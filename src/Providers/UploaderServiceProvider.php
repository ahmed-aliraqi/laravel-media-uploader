<?php

namespace AhmedAliraqi\LaravelMediaUploader\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Elnooronline\LaravelBootstrapForms\Facades\BsForm;
use AhmedAliraqi\LaravelMediaUploader\Support\FFmpegDriver;
use AhmedAliraqi\LaravelMediaUploader\Console\InstallCommand;
use AhmedAliraqi\LaravelMediaUploader\Console\TemporaryClearCommand;
use AhmedAliraqi\LaravelMediaUploader\Forms\Components\ImageComponent;

class UploaderServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();

        $this->registerViews();

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->publishes([
            __DIR__.'/../Resources/assets/images/attach.png' => public_path('/images/attach.png'),
            __DIR__.'/../Resources/assets/images/loading-100.gif' => public_path('/images/loading-100.gif'),
            __DIR__.'/../Resources/assets/images/plus-circle-solid.svg' => public_path('/images/plus-circle-solid.svg'),
        ], 'uploader:icons');

        $this->commands([
            TemporaryClearCommand::class,
            InstallCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('temporary:clean')->everyMinute();
        });

        BsForm::registerComponent('image', ImageComponent::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

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
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->publishes([
            __DIR__.'/../Resources/views' => resource_path('views/vendor/uploader'),
        ], 'uploader:views');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'uploader');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'uploader');
    }
}
