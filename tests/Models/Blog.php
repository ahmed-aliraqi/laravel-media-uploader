<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;

class Blog extends Model implements HasMedia
{
    use HasMediaTrait, HasUploader;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';
}