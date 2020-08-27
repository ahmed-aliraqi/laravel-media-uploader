<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model implements HasMedia
{

    use InteractsWithMedia, HasUploader;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

}
