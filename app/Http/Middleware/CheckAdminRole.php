<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Thêm Auth

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra người dùng đã đăng nhập và có role 'admin' chưa
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            // Nếu không phải admin, chuyển hướng hoặc báo lỗi
            // abort(403, 'Unauthorized action.'); // Cách 1: Báo lỗi 403
            return redirect('/dashboard')->with('error', 'Bạn không có quyền truy cập trang này.'); // Cách 2: Chuyển hướng
        }
        return $next($request);
    }
}