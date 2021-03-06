<?php

namespace Knovators\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Knovators\Support\Traits\APIResponse;

/**
 * Class CreateRequest
 * @package Knovators\Media\Http\Requests
 */
class CreateRequest extends FormRequest
{

    use APIResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $mimeTypes = $this->config('mimes');

        $maxSize = $this->config('max_size');

        return [
            'files'   => 'required_without:file|array',
            'files.*' => "required_with:files|mimes:$mimeTypes|max:$maxSize",
            'file'    => "required_without:files|mimes:$mimeTypes|max:$maxSize",
            'type'    => 'required|string',
            'title'   => 'sometimes|required|string',
            'alt'     => 'sometimes|required|string',
            'link'    => 'sometimes|required|string'
        ];
    }


    /**
     * Get config value by key
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function config($key, $default = null) {
        return config("media.validate.$key", $default);
    }
}
