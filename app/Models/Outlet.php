<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $guarded = [];

    public function setIdAttribute($value){
        $this->attributes['id']= strtoupper($value);
    }
    public function scopeFilterByName($query, $name){
        if(empty($name)) return $query;
        return $query->where("name","LIKE","%{$name}%");
    }
}
