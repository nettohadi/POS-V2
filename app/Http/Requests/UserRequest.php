<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends ApiRequest
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
        $rules = [
            'name' => 'required',
            'sex'  => 'required',
            'email' => 'required|email|unique:users,email,'.request('id'),
            'email_verified_at' => 'nullable',
            'role' => 'required',
            'password' => 'min:6',
            'password_confirm' => 'required_with:password|min:6',
            'image' => 'nullable|image',
            'shift_id' => 'nullable',
            'outlet_id' => 'required|max:5|exists:outlets,id'
        ];

        if(!request('user')) $rules['password'] = 'required|min:6';

        return $rules;
    }
}
