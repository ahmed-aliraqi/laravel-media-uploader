<?php

namespace AhmedAliraqi\LaravelMediaUploader\Http\Requests;

use AhmedAliraqi\LaravelMediaUploader\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['sometimes', 'required', new MediaRule('image', 'video', 'audio', 'document')],
            'files' => ['sometimes', 'required', 'array'],
            'files.*' => ['sometimes', 'required', new MediaRule('image', 'video', 'audio', 'document')],
            'collection' => ['nullable', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
