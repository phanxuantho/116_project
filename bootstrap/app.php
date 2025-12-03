<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //

        // --- THÊM ALIAS CỦA BẠN VÀO ĐÂY - Trang cấu hình hệ thống setting - role admin mới truy cập được ---
        $middleware->alias([
           // 'auth' => \App\Http\Middleware\Authenticate::class,
           // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            // ... các alias mặc định khác ...
            
            // --- THÊM ALIAS CỦA BẠN VÀO ĐÂY ---
            'admin' => \App\Http\Middleware\CheckAdminRole::class, 
            // ------------------------------------
        ]);

    
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
