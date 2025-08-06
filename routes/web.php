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
