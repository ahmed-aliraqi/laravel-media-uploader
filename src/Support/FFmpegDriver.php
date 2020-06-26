<?php

namespace AhmedAliraqi\LaravelMediaUploader\Support;

use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Config;

class FFmpegDriver
{
    /**
     * @var \FFMpeg\FFMpeg
     */
    private $driver;

    /**
     * Create driver instance.
     */
    public function __construct()
    {
        $this->driver = FFMpeg::create([
            'ffmpeg.binaries' => Config::get('medialibrary.ffmpeg_path'),
            'ffprobe.binaries' => Config::get('medialibrary.ffprobe_path'),
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);
    }

    /**
     * @return \FFMpeg\FFMpeg
     */
    public function driver()
    {
        return $this->driver;
    }
}
