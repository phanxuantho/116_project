<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Các route công khai (không cần đăng nhập)
// Route::get('/about', ...);

// Các route cần xác thực (phải đăng nhập)
Route::middleware(['auth'])->group(function () {
    // Route dashboard mặc định của Breeze
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Đặt route danh sách sinh viên của bạn vào đây
    // Định nghĩa một route GET cho URL '/students'.
    // Khi người dùng truy cập URL này, phương thức 'index' trong StudentController sẽ được gọi.
    // ->name('students.index') đặt tên cho route này để dễ dàng gọi trong view.
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');

    // Nhóm route cho Quản lý Sinh viên
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        //`students.edit`: Để hiển thị form chỉnh sửa.
        //`students.update`: Để xử lý dữ liệu khi người dùng nhấn nút "Lưu".
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::patch('/{student}', [StudentController::class, 'update'])->name('update');
        // Route::get('/create', [StudentController::class, 'create'])->name('create'); // Thêm mới
        // Route::post('/', [StudentController::class, 'store'])->name('store');
        // Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit'); // Cập nhật thông tin
        // Route::patch('/{student}', [StudentController::class, 'update'])->name('update');
        // Route::get('/status-update', [StudentController::class, 'statusUpdateView'])->name('status.view'); // Cập nhật trạng thái
        // Route::post('/status-update', [StudentController::class, 'statusUpdateAction'])->name('status.update');
    });
    // Nhóm route cho Quản lý Cấp phát
    Route::prefix('funding')->name('funding.')->group(function () {
        // Route::get('/review-list', [FundingController::class, 'reviewList'])->name('review'); // In danh sách rà soát
        // Route::get('/disbursement', [FundingController::class, 'disbursement'])->name('disbursement'); // Cấp phát kinh phí
    });

    // Nhóm route cho Báo cáo
    Route::prefix('reports')->name('reports.')->middleware('auth')->group(function () {
        // Routes cho chức năng Rà soát danh sách hàng tháng
        Route::get('/monthly-review', [ReportController::class, 'showMonthlyReviewForm'])->name('monthly-review.form');
        Route::get('/monthly-review/export', [ReportController::class, 'exportMonthlyReview'])->name('monthly-review.export');
        Route::get('/monthly-review/print', [ReportController::class, 'printMonthlyReview'])->name('monthly-review.print');
        // THÊM ROUTE MỚI CHO CHỨC NĂNG IN TẤT CẢ
        Route::get('/monthly-review/print-all', [ReportController::class, 'printAllMonthlyReview'])->name('monthly-review.print-all');
        
        
        // THÊM 2 ROUTE báo cáo tổng quan
        Route::get('/overview', [ReportController::class, 'showOverviewForm'])->name('overview.form');
        Route::get('/overview/export', [ReportController::class, 'exportOverview'])->name('overview.export');
        Route::get('/overview/print', [ReportController::class, 'printOverview'])->name('overview.print');
    
    });






    // **ROUTE MỚI**: Route này dùng để cung cấp dữ liệu Lớp học cho JavaScript một cách linh hoạt
    // Nó sẽ được gọi mỗi khi người dùng thay đổi bộ lọc Khoa hoặc Khóa học.
    Route::get('/get-classes-by-filter', [StudentController::class, 'getClasses'])->name('api.get_classes');
    // ROUTE API MỚI ĐỂ LẤY DANH SÁCH XÃ/PHƯỜNG
    Route::get('/get-wards-by-province', [StudentController::class, 'getWards'])->name('api.get_wards');
    
    // Thêm các route cần bảo vệ khác ở đây...
    // Route::get('/profile', ...);
});


// Các route xác thực do Breeze tạo ra sẽ được include ở đây
require __DIR__.'/auth.php';
