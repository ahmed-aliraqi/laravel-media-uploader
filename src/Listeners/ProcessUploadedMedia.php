<?php

namespace AhmedAliraqi\LaravelMediaUploader\Listeners;

use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use RuntimeException;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Image as ImageGenerator;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProcessUploadedMedia implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param MediaHasBeenAdded $event
     * @throws \Exception
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        if (app()->runningUnitTests()) {
            return;
        }

        if ($event->media->getCustomProperty('status') == 'processed') {
            // Skipped Processing Media File
            return;
        }

        try {
            if ($this->isImage($event->media)) {
                $path = $this->processImage($event->media);
            } elseif ($this->isDocument($event->media)) {
                $path = $this->processDocument($event->media);
            } elseif ($this->isVideo($event->media)) {
                $path = $this->processVideo($event->media);
            } elseif ($this->isAudio($event->media)) {
                $path = $this->processAudio($event->media);
            } else {
                $path = null;
            }
            $this->processingDone($event->media, $path);
        } catch (RuntimeException $e) {
            $this->processingFailed($event->media);
        }

        $event->media->setCustomProperty('status', 'processing')->save();
    }

    /**
     * Determine if the media file is an image.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return bool
     */
    protected function isImage(Media $media)
    {
        return (new ImageGenerator())->canHandleMime($media->mime_type);
    }

    /**
     * Determine if the media file is a document.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return bool
     */
    protected function isDocument(Media $media)
    {
        return in_array(
            $media->mime_type,
            Config::get('laravel-media-uploader.documents_mime_types')
        );
    }

    /**
     * Determine if the media file is a video and initiate the required driver.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return bool
     */
    protected function isVideo(Media $media)
    {
        return app('ffmpeg-driver')->open($media->getPath()) instanceof Video;
    }

    /**
     * Determine if the media file is an audio and the initiate required driver.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return bool
     */
    protected function isAudio(Media $media)
    {
        return app('ffmpeg-driver')->open($media->getPath()) instanceof Audio;
    }

    /**
     * Process Image File.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return null
     */
    protected function processImage(Media $media)
    {
        $image = Image::make($media->getPath())->orientate();

        $media
            ->setCustomProperty('type', 'image')
            ->setCustomProperty('width', $image->width())
            ->setCustomProperty('height', $image->height())
            ->setCustomProperty('ratio', (string) round($image->width() / $image->height(), 3))
            ->save();
    }

    /**
     * Process Document File.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return null
     */
    protected function processDocument(Media $media)
    {
        $media->setCustomProperty('type', 'document')->save();
    }

    /**
     * Process Video File.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return string
     */
    protected function processVideo(Media $media)
    {
        $media->setCustomProperty('type', 'video')->save();

        $video = app('ffmpeg-driver')->open($media->getPath());

        $format = new X264();

        $format->on('progress', $this->increaseProcessProgress($media));

        $format->setAudioCodec('aac');

        $format->setAdditionalParameters(['-vf', 'pad=ceil(iw/2)*2:ceil(ih/2)*2']);

        $video->save($format, $processedFile = $this->generatePathForProcessedFile($media, 'mp4'));

        return $processedFile;
    }

    /**
     * Process Audio File.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return string
     */
    protected function processAudio(Media $media)
    {
        $media->setCustomProperty('type', 'audio')->save();

        $audio = app('ffmpeg-driver')->open($media->getPath());

        $format = new Mp3();

        $format->on('progress', $this->increaseProcessProgress($media));

        $audio->save($format, $processedFile = $this->generatePathForProcessedFile($media, 'mp3'));

        return $processedFile;
    }

    /**
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return \Closure
     */
    protected function increaseProcessProgress(Media $media): \Closure
    {
        return function (
            $file,
            $format,
            $percentage
        ) use ($media) {
            // Progress Percentage is $percentage
            $media->setCustomProperty('progress', $percentage);
            $media->save();
        };
    }

    /**
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @param null $processedFilePath
     * @throws \Exception
     * @return void
     */
    protected function processingDone(Media $media, $processedFilePath = null)
    {
        // If the processing does not ended with generating a new file.
        if (is_null($processedFilePath)) {
            $media->setCustomProperty('status', 'processed')
                ->setCustomProperty('progress', 100)
                ->save();
        } else {
            // New Converted Media Will Be Added
            $duration = app('ffmpeg-driver')
                ->getFFProbe()
                ->format($processedFilePath)
                ->get('duration');

            $media->model
                ->addMedia($processedFilePath)
                ->withCustomProperties([
                    'type' => $media->getCustomProperty('type'),
                    'status' => 'processed',
                    'progress' => 100,
                    'duration' => $duration,
                ])
                ->preservingOriginal()
                ->toMediaCollection($media->collection_name);

            (clone $media)->delete();
        }
    }

    /**
     * Mark media status as failed.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     */
    protected function processingFailed(Media $media)
    {
        $media->setCustomProperty('status', 'failed')->save();
    }

    /**
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @param null $extension
     * @return string
     */
    protected function generatePathForProcessedFile(Media $media, $extension = null)
    {
        $path = $media->getPath();

        return pathinfo($path, PATHINFO_DIRNAME)
            .DIRECTORY_SEPARATOR.pathinfo($path, PATHINFO_FILENAME)
            .'.processed.'.$extension;
    }
}
