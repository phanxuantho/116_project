<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('116_student_status_logs', function (Blueprint $table) {
            $table->id();
            
            // Khóa ngoại liên kết với bảng sinh viên
            $table->foreignId('student_code')->constrained('116_students', 'student_code')->onDelete('cascade');
            
            // Khóa ngoại liên kết với người dùng đã thực hiện thay đổi
            $table->foreignId('user_id')->nullable()->constrained('116_users')->onDelete('set null');
            
            // Các trường trạng thái cũ và mới
            $table->enum('status_old', ['Đang học', 'Bảo lưu', 'Tốt nghiệp', 'Thôi học'])->nullable();
            $table->enum('status_new', ['Đang học', 'Bảo lưu', 'Tốt nghiệp', 'Thôi học'])->nullable();
            $table->enum('funding_status_old', ['Đang nhận', 'Tạm dừng nhận', 'Thôi nhận'])->nullable();
            $table->enum('funding_status_new', ['Đang nhận', 'Tạm dừng nhận', 'Thôi nhận'])->nullable();
            
            // Các trường thông tin bổ sung
            $table->text('note')->nullable();
            $table->string('evidence', 150)->nullable();
            $table->date('evidence_date')->nullable();
            $table->string('evidence_file_path')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('116_student_status_logs');
    }
};