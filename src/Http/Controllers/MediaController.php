<?php

namespace AhmedAliraqi\LaravelMediaUploader\Http\Controllers;

use AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile;
use AhmedAliraqi\LaravelMediaUploader\Http\Requests\MediaRequest;
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
                ->usingFileName($this->formatName($request->file))
                ->toMediaCollection($temporaryFile->collection);
        }

        foreach ($request->file('files', []) as $file) {
            $temporaryFile->addMedia($file)
                ->usingFileName($this->formatName($file))
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
     * Get the formatted name of the given file.
     *
     * @param $file
     * @return string
     */
    public function formatName($file)
    {
        $extension = '.'.$file->getClientOriginalExtension();

        $name = trim($file->getClientOriginalName(), $extension);

        $name = $this->replaceNumbers($name);

        return Str::slug($name).$extension;
    }

    /**
     * Convert arabic & persian decimal to valid decimal.
     *
     * @param $string
     * @return string|string[]
     */
    public function replaceNumbers($string)
    {
        $newNumbers = range(0, 9);
        // 1. Persian HTML decimal
        $persianDecimal = [
            '&#1776;',
            '&#1777;',
            '&#1778;',
            '&#1779;',
            '&#1780;',
            '&#1781;',
            '&#1782;',
            '&#1783;',
            '&#1784;',
            '&#1785;',
        ];
        // 2. Arabic HTML decimal
        $arabicDecimal = [
            '&#1632;',
            '&#1633;',
            '&#1634;',
            '&#1635;',
            '&#1636;',
            '&#1637;',
            '&#1638;',
            '&#1639;',
            '&#1640;',
            '&#1641;',
        ];
        // 3. Arabic Numeric
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        // 4. Persian Numeric
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $string = str_replace($persianDecimal, $newNumbers, $string);
        $string = str_replace($arabicDecimal, $newNumbers, $string);
        $string = str_replace($arabic, $newNumbers, $string);

        return str_replace($persian, $newNumbers, $string);
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
