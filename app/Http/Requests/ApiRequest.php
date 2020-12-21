<?php


namespace App\Http\Requests;


use App\Exceptions\ApiValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \App\Exceptions\ApiValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ApiValidationException($validator->errors()->toArray());
    }
}
