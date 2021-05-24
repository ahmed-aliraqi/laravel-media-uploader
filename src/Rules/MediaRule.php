<?php

namespace AhmedAliraqi\LaravelMediaUploader\Rules;

use FFMpeg\Exception\RuntimeException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Image;

class MediaRule implements Rule
{
    /**
     * @var array
     */
    private $types;

    /**
     * @var \FFMpeg\FFMpeg
     */
    private $driver;

    /**
     * Create a new rule instance.
     *
     * @param $types
     */
    public function __construct(...$types)
    {
        $this->types = $types;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param UploadedFile|mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_string($value) && base64_decode(base64_encode($value)) === $value) {
            return true;
        }

        if (! $value instanceof UploadedFile) {
            return false;
        }

        try {
            $type = $this->getTypeString($value);
        } catch (RuntimeException $e) {
            return false;
        }

        return in_array($type, $this->types);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('uploader::validation.invalid');
    }

    /**
     * @param UploadedFile|mixed $value
     * @return string
     */
    protected function getTypeString($value): string
    {
        $fileFullPath = $value->getRealPath();

        if ((new Image())->canHandleMime($value->getMimeType())) {
            $type = 'image';
        } elseif (in_array($value->getMimeType(), $this->documentsMimeTypes())) {
            $type = 'document';
        } else {
            $type = strtolower(class_basename(get_class(
                app('ffmpeg-driver')->open($fileFullPath)
            )));
        }

        return $type; // either: image, video or audio.
    }

    /**
     * The supported mime types for document files.
     *
     * @return string[]
     */
    protected function documentsMimeTypes()
    {
        return Config::get('laravel-media-uploader.documents_mime_types');
    }
}
