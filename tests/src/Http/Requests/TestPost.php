<?php

declare(strict_types=1);

namespace ResourceController\Tests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestPost extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|required|string',
            'body' => 'sometimes|required|string',
            'is_published' => 'sometimes|required|boolean',
        ];
    }
}
