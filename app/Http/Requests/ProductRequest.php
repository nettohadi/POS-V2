<?php

namespace App\Http\Requests;

class ProductRequest extends ApiRequest
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
        return  [
            'id' => 'required',
            'barcode' => '',
            'name' => 'required',
            'name_initial' => '',
            'unit_id' => 'required|numeric|exists:units,id',
            'category_id' => 'required|numeric|exists:categories,id',
            'stock_type' => 'required|string|in:single,composite',
            'primary_ingredient_id' => '',
            'primary_ingredient_qty' => '',
            'for_sale' => 'required|boolean',
            'image' => 'nullable|image',
            'minimum_qty' => '',
            'minimum_expiration_days' => ''
        ];
    }
}
