<?php

namespace EricDowell\ResourceController\Tests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|required',
            'body' => 'sometimes|required',
            'is_published' => 'sometimes|required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'The :attribute is required',
        ];
    }
}
