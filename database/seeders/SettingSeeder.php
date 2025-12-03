<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting; // Import Setting model

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        
        
        Setting::updateOrCreate(['key' => 'app_name'], [
            'value' => config('app.name', 'Ứng dụng 116'),
            'group' => 'general',
            'label' => 'Tên Ứng dụng/Tên Trường',
            'type' => 'text',
        ]);
        
        Setting::updateOrCreate(['key' => 'app_logo'], [
            'value' => null, // Sẽ lưu đường dẫn file logo
            'group' => 'general',
            'label' => 'Logo Ứng dụng',
            'type' => 'file', // Kiểu file
        ]);

        Setting::updateOrCreate(['key' => 'contact_info'], [
            'value' => "Địa chỉ:...\nEmail: support@example.com\nSĐT: 0123456789",
            'group' => 'general',
            'label' => 'Thông tin Liên hệ',
            'type' => 'textarea', // Kiểu textarea
        ]);

        Setting::updateOrCreate(['key' => 'maintenance_mode'], [
            'value' => '0', // 0 = off, 1 = on
            'group' => 'general',
            'label' => 'Chế độ Bảo trì',
            'type' => 'toggle', // Kiểu toggle (on/off)
        ]);

        Setting::updateOrCreate(['key' => 'session_lifetime'], [
            'value' => config('session.lifetime', '120'), // Lấy từ config, mặc định 120 phút
            'group' => 'general',
            'label' => 'Thời gian Hết hạn Session (phút)',
            'type' => 'number', // Kiểu number
        ]);

        // Cấu hình Bật/Tắt Form
        Setting::updateOrCreate(['key' => 'enable_graduate_form'], [
            'value' => '1', // 1 = on, 0 = off
            'group' => 'forms', // Nhóm mới
            'label' => 'Bật Form Khai báo Việc làm Tốt nghiệp',
            'type' => 'toggle',
        ]);

        Setting::updateOrCreate(['key' => 'enable_student_update_form'], [
            'value' => '1', // 1 = on, 0 = off
            'group' => 'forms',
            'label' => 'Bật Form Cập nhật Thông tin Sinh viên',
            'type' => 'toggle',
        ]);
         Setting::updateOrCreate(['key' => 'disable_student_update_inputs'], [
            'value' => '0', // 0 = cho phép nhập, 1 = disable
            'group' => 'forms',
            'label' => 'Vô hiệu hóa Nhập liệu (Chỉ xem) Form Cập nhật SV',
            'type' => 'toggle',
        ]);
    }
}