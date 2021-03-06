<?php

declare(strict_types=1);

namespace ResourceController\Tests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestUser extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }
}
