<?php

namespace App\Models\Base;

use App\Exceptions\ApiNotFoundException;
use App\Libs\ApiResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BaseModel extends Model
{
    protected static function findOrThrow($id){
        $model = self::find($id);

        if(!$model){throw new ApiNotFoundException();}

        return $model;
    }

    public function scopeFindOrThrow($query, $id){
        if(!$id) return $query;

        $model =  $query->whereId($id)->first();

        if(!$model){ throw new ApiNotFoundException();}

        return $model;
    }

    public function scopeFirstOrThrow($query, $message=null)
    {
        $model = $query->first();

        if(!$model) throw new ApiNotFoundException($message);

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

    public static function generateImageName(string $imageExtension='jpg', string $keyword='image'){
        return $keyword.'_'.Carbon::now()->timestamp.Carbon::now()->microsecond.'.'.$imageExtension;
    }

    protected static function uploadImage(array $data, string $imageFieldName='image'){
        if(!$data) return null;

        if($data[$imageFieldName]){
            $imageFile = $data[$imageFieldName];
            $path = static::uploadPath();
            $newFileName = self::generateImageName($imageFile->getClientOriginalExtension());

            $isUploaded = Storage::disk('public')->putFileAs($path, $imageFile, $newFileName);
            $data[$imageFieldName] = $isUploaded ? $path.'/'.$newFileName : null;
        }else{
            //if image is nul, remove image
            unset($data[$imageFieldName]);
        }

        return $data;
    }

    protected static function uploadImageAndCreate(array $data, string $imageFieldName='image'){
        return self::create(self::uploadImage($data, $imageFieldName));
    }

    public function uploadImageAndUpdate(array $data, string $imageFieldName='image'){
        return tap($this)->update(self::uploadImage($data, $imageFieldName));
    }

    protected static function uploadPath(){
        return '/images';
    }
}
