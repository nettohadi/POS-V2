<?php

namespace App\Exceptions;

use App\Libs\ApiResponse;
use Exception;

class ApiAuthorizationException extends Exception
{
    public function __construct($message=null)
    {
        parent::__construct('', 0, null);
        $this->message = $message ?? 'Unauthorized';
    }

    public function render($request){
        return ApiResponse::make()->isNotAuthorized($this->message)->json();
    }
}
