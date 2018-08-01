<?php

namespace EricDowell\ResourceController\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'sometimes|string',
            'username' => 'sometimes|string',
            'email' => 'sometimes|email|required',
            'password' => 'sometimes|string|required|confirmed',
            'current_password' => 'sometimes|string|required',
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
