<?php

namespace AhmedAliraqi\LaravelMediaUploader\Http\Controllers;

use AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile;
use AhmedAliraqi\LaravelMediaUploader\Http\Requests\MediaRequest;
use AhmedAliraqi\LaravelMediaUploader\Support\Uploader;
use AhmedAliraqi\LaravelMediaUploader\Transformers\MediaResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $modelClass = Config::get(
            'media-library.media_model',
            \Spatie\MediaLibrary\MediaCollections\Models\Media::class
        );

        $tokens = is_array(request('tokens')) ? request('tokens') : [];

        $media = $modelClass::whereHasMorph(
            'model',
            [TemporaryFile::class],
            function (Builder $builder) use ($tokens) {
                $builder->whereIn('token', $tokens);
                $builder->when(request('collection'), function (Builder $builder) {
                    $builder->where(request()->only('collection'));
                });
            }
        )->get();

        return MediaResource::collection($media);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \AhmedAliraqi\LaravelMediaUploader\Http\Requests\MediaRequest  $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function store(MediaRequest $request)
    {
        /** @var \AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile $temporaryFile */
        $temporaryFile = TemporaryFile::create([
            'token' => Str::random(60),
            'collection' => $request->input('collection', 'default'),
        ]);

        if (is_string($request->file) && base64_decode(base64_encode($request->file)) === $request->file) {
            $temporaryFile->addMediaFromBase64($request->file)
                ->usingFileName(time().'.png')
                ->toMediaCollection($temporaryFile->collection);
        }

        if ($request->hasFile('file')) {
            $temporaryFile->addMedia($request->file)
                ->usingFileName(Uploader::formatName($request->file))
                ->toMediaCollection($temporaryFile->collection);
        }

        foreach ($request->file('files', []) as $file) {
            $temporaryFile->addMedia($file)
                ->usingFileName(Uploader::formatName($file))
                ->toMediaCollection($temporaryFile->collection);
        }

        return MediaResource::collection(
            $temporaryFile->getMedia(
                $temporaryFile->collection ?: 'default'
            )
        )->additional([
            'token' => $temporaryFile->token,
        ]);
    }

    /**
     * @param $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($media)
    {
        $modelClass = Config::get(
            'media-library.media_model',
            \Spatie\MediaLibrary\MediaCollections\Models\Media::class
        );

        $media = $modelClass::findOrFail($media);

        $media->delete();

        return response()->json([
            'message' => 'deleted',
        ]);
    }
}
