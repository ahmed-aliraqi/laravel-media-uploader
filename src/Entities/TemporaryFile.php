<?php

namespace AhmedAliraqi\LaravelMediaUploader\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryFile extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'collection',
    ];

    /**
     * Register the conversions for the specified model.
     *
     * @param  \Spatie\MediaLibrary\MediaCollections\Models\Media|null  $media
     *
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(70)
             ->format('png');

        $this->addMediaConversion('small')
             ->width(120)
             ->format('png');

        $this->addMediaConversion('medium')
             ->width(160)
             ->format('png');

        $this->addMediaConversion('large')
             ->width(320)
             ->format('png');
    }
}
