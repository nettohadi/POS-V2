<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
    use HasFactory;
    protected $fillable = ['name','desc','type_id'];

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function scopeFilterByName($query, $name){

        if(empty($name)) return $query;

        return $query->where('name','like',"%{$name}%");
    }
}
