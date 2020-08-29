<?php

namespace AhmedAliraqi\LaravelMediaUploader\Jobs;

use FFMpeg\Exception\RuntimeException;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\Conversions\FileManipulator;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Image as ImageGenerator;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob as BasePerformConversions;

class PerformConversions extends BasePerformConversions
{
    public function handle(FileManipulator $fileManipulator): bool
    {
        // Conversion Done...
        if ($this->media->getCustomProperty('status') == 'processed') {
            // Skipped Processing Media File
            return parent::handle($fileManipulator);
        }

        $this->media->setCustomProperty('status', 'processing')->save();

        try {
            if ($this->isImage()) {
                $path = $this->processImage();
            } elseif ($this->isDocument()) {
                $path = $this->processDocument();
            } elseif ($this->isVideo()) {
                $path = $this->processVideo();
            } elseif ($this->isAudio()) {
                $path = $this->processAudio();
            } else {
                $path = null;
            }
            $this->processingDone($path);
        } catch (RuntimeException $e) {
            $this->processingFailed();
        }

        return true;
    }

    /**
     * Determine if the media file is an image.
     *
     * @return bool
     */
    protected function isImage()
    {
        return (new ImageGenerator())->canHandleMime($this->media->mime_type);
    }

    /**
     * Determine if the media file is a document.
     *
     * @return bool
     */
    protected function isDocument()
    {
        return in_array(
            $this->media->mime_type,
            Config::get('laravel-media-uploader.documents_mime_types')
        );
    }

    /**
     * Determine if the media file is a video and initiate the required driver.
     *
     * @return bool
     */
    protected function isVideo()
    {
        return app('ffmpeg-driver')->open($this->media->getPath()) instanceof Video;
    }

    /**
     * Determine if the media file is an audio and the initiate required driver.
     *
     * @return bool
     */
    protected function isAudio()
    {
        return app('ffmpeg-driver')->open($this->media->getPath()) instanceof Audio;
    }

    /**
     * Process Image File.
     *
     * @return null
     */
    protected function processImage()
    {
        $image = Image::make($this->media->getPath())->orientate();

        $this->media
            ->setCustomProperty('type', 'image')
            ->setCustomProperty('width', $image->width())
            ->setCustomProperty('height', $image->height())
            ->setCustomProperty('ratio', (string) round($image->width() / $image->height(), 3))
            ->save();
    }

    /**
     * Process Document File.
     *
     * @return null
     */
    protected function processDocument()
    {
        $this->media->setCustomProperty('type', 'document')->save();
    }

    /**
     * Process Video File.
     *
     * @return string
     */
    protected function processVideo()
    {
        $this->media->setCustomProperty('type', 'video')->save();

        $video = app('ffmpeg-driver')->open($this->media->getPath());

        $format = new X264();

        $format->on('progress', $this->increaseProcessProgress());

        $format->setAudioCodec('aac');

        $format->setAdditionalParameters(['-vf', 'pad=ceil(iw/2)*2:ceil(ih/2)*2']);

        $video->save($format, $processedFile = $this->generatePathForProcessedFile('mp4'));

        return $processedFile;
    }

    /**
     * Process Audio File.
     *
     * @return string
     */
    protected function processAudio()
    {
        $this->media->setCustomProperty('type', 'audio')->save();

        $audio = app('ffmpeg-driver')->open($this->media->getPath());

        $format = new Mp3();

        $format->on('progress', $this->increaseProcessProgress());

        $audio->save($format, $processedFile = $this->generatePathForProcessedFile('mp3'));

        return $processedFile;
    }

    /**
     * @return \Closure
     */
    protected function increaseProcessProgress(): \Closure
    {
        return function (
            $file,
            $format,
            $percentage
        ) {
            // Progress Percentage is $percentage
            $this->media->setCustomProperty('progress', $percentage);
            $this->media->save();
        };
    }

    /**
     * @param null $processedFilePath
     * @throws \Exception
     * @return null
     */
    protected function processingDone($processedFilePath = null)
    {
        $oldMedia = $this->media;

        $model = $oldMedia->model;

        // If the processing does not ended with generating a new file.
        if (is_null($processedFilePath)) {
            $oldMedia->setCustomProperty('status', 'processed')
                ->setCustomProperty('progress', 100)
                ->save();
        } else {
            // New Converted Media Will Be Added
            $duration = app('ffmpeg-driver')
                ->getFFProbe()
                ->format($processedFilePath)
                ->get('duration');

            $model
                ->addMedia($processedFilePath)
                ->withCustomProperties([
                    'type' => $oldMedia->getCustomProperty('type'),
                    'status' => 'processed',
                    'progress' => 100,
                    'duration' => $duration,
                ])
                ->preservingOriginal()
                ->toMediaCollection($oldMedia->collection_name);

            $oldMedia->delete();
        }
    }

    /**
     * Mark media status as failed.
     */
    protected function processingFailed()
    {
        $media = $this->media;

        $media->setCustomProperty('status', 'failed')->save();
    }

    /**
     * @param null $extension
     * @return string
     */
    protected function generatePathForProcessedFile($extension = null)
    {
        $path = $this->media->getPath();

        return pathinfo($path, PATHINFO_DIRNAME)
            .DIRECTORY_SEPARATOR.pathinfo($path, PATHINFO_FILENAME)
            .'.processed.'.$extension;
    }
}
