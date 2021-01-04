<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Builder;

class Recipe extends BaseModel
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    public function scopeFilterByProductName($query, $productName)
    {
        if(empty($productName)) return $query;

        return $query->whereHas('product',function ($subQuery) use ($productName){
            $subQuery->where('name',"Like","%{$productName}%");
        });
    }

    public function scopeFilterByOutlet($query, $outletId)
    {
        if(empty($outletId)) return $query;

        return $query->whereHas('outlet',function ($subQuery) use ($outletId){
            $subQuery->whereOutletId($outletId);
        });
    }
}
