<?php

namespace App\Http\Controllers\Base;

use App\Exceptions\ApiValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    public function validateRequest(Request $request, Array $rules){
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            throw new ApiValidationException($validator->errors()->toArray());
        }

        return $validator->validated();
    }

}
