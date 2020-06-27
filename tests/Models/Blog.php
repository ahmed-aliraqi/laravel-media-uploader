<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests\Models;

use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

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
