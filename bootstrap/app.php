<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'classroom.enrollment' => \App\Http\Middleware\CheckClassroomEnrollment::class,
            'classroom.content.lock' => \App\Http\Middleware\CheckPrevContent::class,
        ]);

        $middleware->trustProxies(
            at: '*',
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // when exception page expired, redirect to home
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if ($response->getStatusCode() === 419) {
                return redirect('/')
                    ->with('error', 'Sesi kamu sudah kadaluarsa. Silakan coba lagi.');
            }
            return $response;
        });
    })->create();
