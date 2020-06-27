<?php

namespace AhmedAliraqi\LaravelMediaUploader\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use AhmedAliraqi\LaravelMediaUploader\Tests\TestCase;
use AhmedAliraqi\LaravelMediaUploader\Tests\Models\Blog;

class UploaderFeatureTest extends TestCase
{
    /** @test */
    public function it_can_upload_and_display_temporary_files()
    {
        Storage::fake('public');

        $response = $this->postJson(url('/api/uploader/media/upload'), [
            'files' => [UploadedFile::fake()->create('thumbnail.jpg', 200)],
            'collection' => 'images',
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'url',
                    'preview',
                    'name',
                    'file_name',
                    'type',
                    'type',
                    'mime_type',
                    'size',
                    'human_readable_size',
                    'status',
                    'links',
                ],
            ],
        ]);

        // Display recently uploaded files via token.

        $response = $this->getJson(
            url('/api/uploader/media').'?tokens[]='.$response->json('token')
        );

        $response->assertSuccessful();

        $this->assertEquals(1, count($response->json('data')));
    }

    /** @test */
    public function it_can_delete_uploaded_files()
    {
        Storage::fake('public');

        /** @var Blog $blog */
        $blog = Blog::create();

        $blog->addMedia(
            UploadedFile::fake()
                ->create('thumbnail.jpg', 200)
        )->toMediaCollection();

        $this->assertEquals(1, $blog->getMedia()->count());

        $this->deleteJson(url('/api/uploader/media/'.$blog->getFirstMedia()->id));

        $blog->refresh();

        $this->assertEquals(0, $blog->getMedia()->count());
    }
}
