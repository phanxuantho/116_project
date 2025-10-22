<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('116_graduate_employment', function (Blueprint $table) {
            $table->id();
            // Liên kết với bảng sinh viên, đảm bảo khi xóa SV thì xóa cả bản ghi này
           // $table->foreignId('student_code')->constrained('116_students', 'student_code')->onDelete('cascade');
            $table->bigInteger('student_code'); // Định nghĩa rõ là BIGINT (signed)
            $table->foreign('student_code')      // Định nghĩa khóa ngoại
                ->references('student_code')   // Tham chiếu đến cột student_code
                ->on('116_students')         // Trên bảng 116_students
                ->onDelete('cascade');       // Tùy chọn khi xóa (giữ nguyên)
            
            $table->enum('employment_status', ['Đã có việc làm', 'Chưa có việc làm', 'Đang học nâng cao', 'Khác'])->comment('Tình trạng việc làm');
            $table->string('job_title')->nullable()->comment('Chức danh/Vị trí công việc');
            $table->string('company_name')->nullable()->comment('Tên cơ quan/Công ty');
            $table->text('company_address')->nullable()->comment('Địa chỉ cơ quan/Công ty');
            $table->enum('employment_type', ['Đúng ngành đào tạo', 'Trái ngành đào tạo'])->nullable()->comment('Loại hình công việc');
            $table->date('start_date')->nullable()->comment('Ngày bắt đầu làm việc');
            $table->enum('contract_type', ['Hợp đồng xác định thời hạn', 'Hợp đồng không xác định thời hạn', 'Khác'])->nullable()->comment('Loại hợp đồng');
            // Cột quan trọng để xác định có làm trong ngành giáo dục/địa phương không
            $table->boolean('is_teaching_related')->nullable()->default(null)->comment('Công việc thuộc ngành giáo dục/địa phương');
            $table->string('teaching_location')->nullable()->comment('Địa phương công tác (nếu có)'); // Thêm trường này

            // Thông tin liên hệ cập nhật
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable()->comment('Địa chỉ liên hệ hiện tại');

            $table->text('notes')->nullable()->comment('Ghi chú thêm');
            $table->timestamp('declaration_date')->nullable()->comment('Ngày khai báo/cập nhật');
            $table->timestamps(); // created_at, updated_at

             // Đảm bảo mỗi sinh viên chỉ có 1 bản ghi khai báo (có thể update)
            $table->unique('student_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('116_graduate_employment');
    }
};
