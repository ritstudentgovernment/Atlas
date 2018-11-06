<?php

namespace App\Exceptions;

use Aacotroneo\Saml2\Facades\Saml2Auth;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @throws Exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // For a testing environment, the request is not expectingJson so this if would be bypassed, and redirected.
        // That is less than ideal because PHPUnit doesn't like it when you change headers, so lets check for a testing
        // environment here.
        if ($request->expectsJson() || env('APP_ENV') == 'testing') {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return Saml2Auth::login();
    }
}
