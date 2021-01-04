<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends BaseModel
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function setIdAttribute($value){
        $this->attributes['id']= strtoupper($value);
    }
    public function scopeFilterByName($query, $name){
        if(empty($name)) return $query;
        return $query->where("name","LIKE","%{$name}%");
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
