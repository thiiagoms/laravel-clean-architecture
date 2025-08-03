<?php

namespace App\Infrastructure\Exceptions;

use App\Application\UseCases\Auth\Exception\InvalidCredentialsException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [
        //
    ];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(fn (Throwable $e): JsonResponse => match (true) {
            $e instanceof InvalidCredentialsException, $e instanceof AuthenticationException => response()->json(
                data: ['error' => $e->getMessage()], status: Response::HTTP_UNAUTHORIZED
            ),
            $e instanceof AccessDeniedHttpException => response()->json(
                data: ['error' => $e->getMessage()], status: Response::HTTP_FORBIDDEN
            ),
            $e instanceof NotFoundHttpException => response()->json(
                data: ['error' => 'resource not found'], status: Response::HTTP_NOT_FOUND
            ),
            default => dd($e)
        });
    }
}
