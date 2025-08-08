<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('116_students', function (Blueprint $table) {
            // Thêm cột mới 'funding_status' sau cột 'status'
            $table->enum('funding_status', ['Đang nhận', 'Tạm dừng nhận', 'Thôi nhận'])
                  ->default('Đang nhận')
                  ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('116_students', function (Blueprint $table) {
            $table->dropColumn('funding_status');
        });
    }
};
