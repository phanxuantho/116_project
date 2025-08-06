<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('welcome');
});


// Định nghĩa một route GET cho URL '/students'.
// Khi người dùng truy cập URL này, phương thức 'index' trong StudentController sẽ được gọi.
// ->name('students.index') đặt tên cho route này để dễ dàng gọi trong view.
Route::get('/students', [StudentController::class, 'index'])->name('students.index');

// **ROUTE MỚI**: Route này dùng để cung cấp dữ liệu Lớp học cho JavaScript một cách linh hoạt
// Nó sẽ được gọi mỗi khi người dùng thay đổi bộ lọc Khoa hoặc Khóa học.
Route::get('/get-classes-by-filter', [StudentController::class, 'getClasses'])->name('api.get_classes');
