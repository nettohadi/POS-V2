<?php

namespace App\Exceptions;

use App\Libs\ApiResponse;
use Exception;

class ApiAuthenticationException extends Exception
{
    public function __construct($message=null)
    {
        parent::__construct('', 0, null);
        $this->message = $message ?? 'Unauthenticated';
    }

    public function render($request){
        return ApiResponse::make()->isNotAuthenticated($this->message)->json();
    }
}
