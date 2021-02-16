<?php

namespace AhmedAliraqi\LaravelMediaUploader\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Conversions\ConversionCollection;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->getFullUrl(),
            'preview' => $this->getPreviewUrl(),
            'name' => $this->name,
            'file_name' => $this->file_name,
            'type' => $this->getType(),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'human_readable_size' => $this->human_readable_size,
            'details' => $this->mediaDetails(),
            'status' => $this->mediaStatus(),
            'progress' => $this->when($this->mediaStatus() == 'processing', $this->getCustomProperty('progress')),
            'conversions' => $this->when(
                ($this->isImage() || $this->isVideo()) && ! empty($this->getConversions()),
                $this->getConversions()
            ),
            'links' => [
                'delete' => [
                    'href' => url('api/uploader/media/'.$this->getRouteKey()),
                    'method' => 'DELETE',
                ],
            ],
        ];
    }

    /**
     * Get the generated conversions links.
     *
     * @return array
     */
    public function getConversions()
    {
        $results = [];

        foreach (array_keys($this->getGeneratedConversions()->toArray()) as $conversionName) {
            $conversion = ConversionCollection::createForMedia($this->resource)
                ->first(fn (Conversion $conversion) => $conversion->getName() === $conversionName);

            if ($conversion) {
                $results[$conversionName] = $this->getFullUrl($conversionName);
            }
        }

        return $results;
    }

    /**
     * Determine if the media type is video.
     *
     * @return bool
     */
    public function isVideo()
    {
        return $this->getType() == 'video';
    }

    /**
     * Determine if the media type is image.
     *
     * @return bool
     */
    public function isImage()
    {
        return $this->getType() == 'image';
    }

    /**
     * Determine if the media type is audio.
     *
     * @return bool
     */
    public function isAudio()
    {
        return $this->getType() == 'audio';
    }

    /**
     * Get the media type.
     *
     * @return mixed|string
     */
    public function getType()
    {
        return $this->getCustomProperty('type') ?: $this->type;
    }

    /**
     * Get the preview url.
     *
     * @return string|void
     */
    public function getPreviewUrl()
    {
        if ($this->getType() == 'image') {
            return $this->getFullUrl();
        }

        return 'https://cdn.jsdelivr.net/npm/laravel-file-uploader/dist/img/attach.png';
    }

    /**
     * @return array
     */
    protected function mediaDetails(): array
    {
        $duration = (float) $this->getCustomProperty('duration');

        return [
            $this->mergeWhen($this->isImage(), [
                'width' => $this->getCustomProperty('width'),
                'height' => $this->getCustomProperty('height'),
                'ratio' => (float) $this->getCustomProperty('ratio'),
            ]),
            'duration' => $this->when($this->isVideo() || $this->isAudio(), $duration),
        ];
    }

    /**
     * @return mixed
     */
    protected function mediaStatus()
    {
        return $this->getCustomProperty('status');
    }
}
