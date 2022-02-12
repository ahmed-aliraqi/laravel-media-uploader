<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests;

use AhmedAliraqi\LaravelMediaUploader\Providers\UploaderServiceProvider;
use Illuminate\Console\Application;
use Laraeast\LaravelBootstrapForms\Providers\BootstrapFormsServiceProvider;
use Laraeast\LaravelLocales\Providers\LocalesServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\MediaLibrary\Conversions\Commands\RegenerateCommand;

class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        Application::starting(function ($artisan) {
            $artisan->resolveCommands([
                RegenerateCommand::class,
            ]);
        });
    }

    /**
     * Load package service provider.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            UploaderServiceProvider::class,
            BootstrapFormsServiceProvider::class,
            LocalesServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('media-library', require __DIR__.'/config/media-library.php');
        $app['config']->set('laravel-media-uploader', require __DIR__.'/config/laravel-media-uploader.php');
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
