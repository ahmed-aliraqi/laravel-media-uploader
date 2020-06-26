<?php

namespace AhmedAliraqi\LaravelMediaUploader\Entities\Concerns;

use Illuminate\Support\Facades\Artisan;
use AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile;
use AhmedAliraqi\LaravelMediaUploader\Transformers\MediaResource;

trait HasUploader
{
    /**
     * Assign all uploaded temporary files to the model.
     *
     * @return void
     */
    public function addAllMediaFromTokens()
    {
        $tokens = is_array(request('media')) ? request('media') : [];

        TemporaryFile::whereIn('token', $tokens)
            ->each(function (TemporaryFile $file) {
                foreach ($file->getMedia($file->collection) as $media) {
                    $media->forceFill([
                        'model_type' => $this->getMorphClass(),
                        'model_id' => $this->getKey(),
                    ])->save();

                    Artisan::call('medialibrary:regenerate', [
                        '--ids' => $media->id,
                    ]);
                }

                $file->delete();
            });
    }

    /**
     * Get all the model media of the given collection using "MediaResource".
     *
     * @param string $collection
     * @return \Illuminate\Support\Collection
     */
    public function getMediaResource($collection = 'default')
    {
        return collect(
            MediaResource::collection(
                $this->getMedia($collection)
            )->jsonSerialize()
        );
    }
}
