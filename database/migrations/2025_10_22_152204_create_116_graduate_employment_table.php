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
            
            // Fix 1: Dùng bigInteger (signed) để khớp với student_code của 116_students
            $table->bigInteger('student_code'); 

            $table->enum('employment_status', ['Đã có việc làm', 'Chưa có việc làm', 'Đang học nâng cao', 'Khác'])->comment('Tình trạng việc làm');
            $table->string('job_title')->nullable()->comment('Chức danh/Vị trí công việc');
            $table->string('company_name')->nullable()->comment('Tên cơ quan/Công ty');
            $table->text('company_address')->nullable()->comment('Địa chỉ cơ quan/Công ty');
            $table->enum('employment_type', ['Đúng ngành đào tạo', 'Trái ngành đào tạo'])->nullable()->comment('Loại hình công việc');
            $table->date('start_date')->nullable()->comment('Ngày bắt đầu làm việc');
            $table->enum('contract_type', ['Hợp đồng xác định thời hạn', 'Hợp đồng không xác định thời hạn', 'Khác'])->nullable()->comment('Loại hợp đồng');
            $table->boolean('is_teaching_related')->nullable()->default(null)->comment('Công việc thuộc ngành giáo dục/địa phương');

            // Fix 2: Thêm các cột mới (địa chỉ, tỉnh công tác)
            // Fix 3: Thêm charset và collation để khớp với các bảng import
            
            $table->string('teaching_province_code', 20)->nullable()
                  ->charset('utf8')->collation('utf8_general_ci') // Fix 3
                  ->comment('Mã tỉnh công tác');
            
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            
            $table->string('contact_province_code', 20)->nullable()
                  ->charset('utf8')->collation('utf8_general_ci') // Fix 3
                  ->comment('Mã tỉnh liên hệ');
            
            $table->string('contact_ward_code', 20)->nullable()
                  ->charset('utf8')->collation('utf8_general_ci') // Fix 3
                  ->comment('Mã xã/phường liên hệ');

            $table->string('contact_address_detail')->nullable()->comment('Số nhà, đường, thôn/xóm');
            
            $table->text('notes')->nullable()->comment('Ghi chú thêm');
            $table->timestamp('declaration_date')->nullable()->comment('Ngày khai báo/cập nhật');
            $table->timestamps(); 

            $table->unique('student_code');
            
            // Định nghĩa các khóa ngoại
            $table->foreign('student_code')->references('student_code')->on('116_students')->onDelete('cascade');
            $table->foreign('teaching_province_code')->references('code')->on('116_provinces');
            $table->foreign('contact_province_code')->references('code')->on('116_provinces');
            $table->foreign('contact_ward_code')->references('code')->on('116_wards');
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