<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Contracts\Authentication;

class UpdateTagsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'tags' => 'array|min:2',
            'tags.*.id' => 'required|exists:tag__tags,id',
            'tags.*.enabled' => 'required|boolean'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
