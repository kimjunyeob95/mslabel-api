<?php

namespace App\Exceptions;

use App\Constants\HttpConstant;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // 요청 빈도 제한 예외를 확인
        if ($exception instanceof ThrottleRequestsException) {
            return helpers_json_response(HttpConstant::INTERNAL_SERVER_ERROR, [], HttpConstant::ERROR_MESSAGE_TOO_MANY_REQUEST);
        }

        // 404 Not Found 예외 처리
        if ($exception instanceof NotFoundHttpException) {
            return helpers_json_response(HttpConstant::NOT_FOUND, [], HttpConstant::ERROR_MESSAGE_NOT_FOUND);
        }

        return parent::render($request, $exception);
    }
}
