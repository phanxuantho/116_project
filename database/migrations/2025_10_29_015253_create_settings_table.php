<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary(); // Tên cấu hình (vd: app_name)
            $table->text('value')->nullable(); // Giá trị cấu hình
            $table->string('group')->default('general')->index(); // Nhóm cấu hình (vd: general, forms)
            $table->string('label'); // Tên hiển thị trên form (vd: Tên ứng dụng)
            $table->string('type')->default('text'); // Kiểu input (text, textarea, file, toggle, number)
            $table->text('options')->nullable(); // Tùy chọn bổ sung (vd: cho kiểu toggle)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};