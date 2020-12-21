<?php

namespace App\Exceptions;

use App\Libs\ApiResponse;
use Exception;

class ApiNotFoundException extends Exception
{
    public function render($request){
        return ApiResponse::make()->isNotFound()->json();
    }
}
