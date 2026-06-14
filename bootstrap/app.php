<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    channels: __DIR__ . '/../routes/channels.php',
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    // $middleware->web(LocaleMiddleware::class);
    $middleware->alias([
      'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
      'permission' => \App\Http\Middleware\PermissionMiddleware::class,
      'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
      'status' => \App\Http\Middleware\StatusMiddleware::class,
      'twofactor' => \App\Http\Middleware\TwoFactorMiddleware::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();
