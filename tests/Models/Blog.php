<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests\Models;

use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Blog extends Model implements HasMedia
{
    use InteractsWithMedia, HasUploader;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

    /**
     * Define the media collections.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('default')
            ->onlyKeepLatest(2);
    }
}
