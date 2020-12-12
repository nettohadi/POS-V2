<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    use HasFactory;
    public $incrementing = false;
    protected $guarded = [];

    public function scopeFilterByName($query, $name){
        if(empty($name)) return $query;
        return $query->where('name','LIKE',"%{$name}%");
    }
    public function getForSaleAttribute($value)
    {
        return $value == 1 ? true : false;
    }


}
