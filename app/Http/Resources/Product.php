<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'name_initial' => $this->name_initial,
            'unit_id' => $this->unit_id,
            'category_id' => $this->category_id,
            'stock_type' => $this->stock_type,
            'primary_ingredient_id' => $this->primary_ingredient_id,
            'primary_ingredient_qty' => $this->primary_ingredient_qty,
            'for_sale' => $this->for_sale == 1 ? true : false,
            'image' => $this->image,
            'minimum_qty' => $this->minimum_qty,
            'minimum_expiration_days' => $this->minimum_expiration_days,
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->format('d-m-Y'),

        ];
    }
}
