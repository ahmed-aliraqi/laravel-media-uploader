<?php

namespace AhmedAliraqi\LaravelMediaUploader\Providers;

use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;
use AhmedAliraqi\LaravelMediaUploader\Listeners\ProcessUploadedMedia;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MediaHasBeenAdded::class => [
            ProcessUploadedMedia::class,
        ],
    ];

}
