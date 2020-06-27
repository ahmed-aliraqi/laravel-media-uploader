<?php

namespace AhmedAliraqi\LaravelMediaUploader\Entities;

use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class TemporaryFile extends Model implements HasMedia
{
    use HasMediaTrait;

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
     * @param \Spatie\MediaLibrary\Models\Media $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
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
