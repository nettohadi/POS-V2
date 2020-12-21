<?php

namespace App\Models\Base;

use App\Exceptions\ApiNotFoundException;
use App\Libs\ApiResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected static function tryToFind($id){
        $model = self::find($id);

        if(!$model){throw new ApiNotFoundException();}

        return $model;
    }
    public function scopeTryToFind($query, $id){
        if(!$id) return $query;

        $model =  $query->whereId($id)->first();

        if(!$model){ throw new ApiNotFoundException();}

        return $model;
    }
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
