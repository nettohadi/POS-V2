<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (ApiValidationException $e, $request) {
            return $e->render($request);
        });

        $this->renderable(function (ApiNotFoundException $e, $request){
            return $e->render($request);
        });

        $this->renderable(function (ApiActionException $e, $request){
            return $e->render($request);
        });

        $this->renderable(function (ApiAuthenticationException $e, $request){
            return $e->render($request);
        });

        $this->renderable(function (ApiAuthorizationException $e, $request){
            return $e->render($request);
        });
    }
}
