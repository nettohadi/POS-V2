<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutletRequest extends ApiRequest
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
            'id'             => 'required|string|alpha|max:5',
            'name'           =>'required|string|max:30',
            'address'        =>'required|string|max:100',
            'vat_percentage' => 'nullable|integer|numeric|min:0|max:100',
            'dp_percentage'  => 'nullable|integer|numeric|min:0|max:100',
            'social_media'   => 'nullable|string',
            'contact'        => 'nullable|string'
        ];
    }
}
