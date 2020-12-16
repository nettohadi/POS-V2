<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    use HasFactory;
    public $incrementing = false;
    protected $guarded = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($product){
            $ucode = resolve('App\Models\UCode');
            $dateYearMonth = date('dym');
            $product->id = $product->id ?? $ucode->generate("{$dateYearMonth}");
        });
    }

    public function scopeFilterByName($query, $name){
        if(empty($name)) return $query;
        return $query->where('name','LIKE',"%{$name}%");
    }
    public function getForSaleAttribute($value)
    {
        return $value == 1 ? true : false;
    }

    public static function generateImageName(string $keyword='',string $imageExtension='jpg'){
        return $keyword.'_'.Carbon::now()->timestamp.Carbon::now()->microsecond.'.'.$imageExtension;
    }

    //Relationship : Belongs to Unit
    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    //Relationship : Belongs to Category
    public function category(){
        return $this->belongsTo(Category::class);
    }


}
