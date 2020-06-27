<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use AhmedAliraqi\LaravelMediaUploader\Tests\TestCase;
use AhmedAliraqi\LaravelMediaUploader\Tests\Models\Blog;
use AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile;

class UploaderUnitTest extends TestCase
{
    public function testGetMediaResource()
    {
        Storage::fake('public');

        /** @var Blog $blog */
        $blog = Blog::create();

        $blog->addMedia(
            UploadedFile::fake()
                ->create('thumbnail.jpg', 200)
        )->toMediaCollection();

        $this->assertInstanceOf(Collection::class, $blog->getMediaResource());
    }

    public function testAddAllMediaFromToken()
    {
        Storage::fake('public');

        /** @var Blog $blog */
        $blog = Blog::create();

        $tmp = TemporaryFile::create([
            'token' => 123,
            'collection' => 'default',
        ]);

        $tmp->addMedia(
            UploadedFile::fake()
                ->create('thumbnail.jpg', 200)
        )->toMediaCollection();

        $media = $tmp->getFirstMedia('default');

        $this->assertEquals($media->model_type, TemporaryFile::class);
        $this->assertEquals($media->model_id, $tmp->id);

        $blog->addAllMediaFromTokens([123], 'avatars');

        $media->refresh();

        $this->assertEquals($media->model_type, TemporaryFile::class);
        $this->assertEquals($media->model_id, $tmp->id);

        $blog->addAllMediaFromTokens([123]);

        $media->refresh();

        $this->assertEquals($media->model_type, Blog::class);
        $this->assertEquals($media->model_id, $blog->id);
    }
}
