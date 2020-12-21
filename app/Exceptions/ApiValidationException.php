<?php

namespace App\Exceptions;

use App\Libs\ApiResponse;
use Exception;
use Throwable;

class ApiValidationException extends Exception
{
    /**
     * @var array
     */
    private $errors;

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __construct(Array $errors=[])
    {
        parent::__construct('', 0, null);
        $this->errors = $errors;
    }

    public function render($request)
    {
        return ApiResponse::make()->isNotValid($this->errors)->json();
    }
}
