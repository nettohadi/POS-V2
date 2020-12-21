<?php

namespace App\Exceptions;

use App\Libs\ApiResponse;
use Exception;
use Throwable;

class ApiActionException extends Exception
{
    public function __construct($message)
    {
        parent::__construct('', 0, null);
        $this->message = $message;
    }

    public function render($request){
        return ApiResponse::make()->isNotAllowed($this->message)->json();
    }
}
