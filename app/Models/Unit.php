<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends BaseModel
{
    use HasFactory;
    protected $fillable = ['name','desc'];

    public function scopeFilterByName($query, $name){

        if(empty($name)) return $query;

        return $query->where('name','like',"%{$name}%");
    }
}
