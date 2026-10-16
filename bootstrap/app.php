<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureAccountIsActive;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'active' => EnsureAccountIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = $e instanceof AuthenticationException ? 401
                : ($e instanceof ValidationException ? 422
                : ($e instanceof ModelNotFoundException ? 404
                : ($e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500)));

            return response()->json([
                'success' => false,
                'message' => match ($status) {
                    401 => 'Chưa đăng nhập.',
                    403 => 'Không có quyền truy cập.',
                    404 => 'Không tìm thấy dữ liệu.',
                    422 => 'Dữ liệu không hợp lệ.',
                    429 => 'Quá nhiều yêu cầu.',
                    default => $status === 500 ? 'Lỗi máy chủ.' : 'Yêu cầu không thành công.',
                },
                'errors' => $e instanceof ValidationException ? $e->errors() : (object) [],
            ], $status);
        });
    })->create();
