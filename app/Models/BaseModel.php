<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected function getCreatedAtAttribute($value)
    {
        if(!$value) return null;
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format('d/m/Y H:i:s');
    }

    protected function getUpdatedAtAttribute($value)
    {
        if(!$value) return null;
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format('d/m/Y H:i:s');
    }
}
