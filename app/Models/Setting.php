<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key'; // Chỉ định khóa chính là 'key'
    public $incrementing = false; // Khóa chính không tự tăng
    protected $keyType = 'string'; // Kiểu dữ liệu của khóa chính

    protected $fillable = [
        'key', 
        'value', 
        'group', 
        'label', 
        'type', 
        'options'
    ];

    /**
     * Cast value based on type (ví dụ: boolean cho toggle)
     * Có thể mở rộng thêm nếu cần
     */
    protected $casts = [
        // Ví dụ: cast 'value' thành boolean nếu 'type' là 'toggle'
        // 'value' => 'string', // Mặc định là string, sẽ xử lý khi lưu/lấy
    ];
    
    // Helper để lấy giá trị theo key
    public static function getValue(string $key, $default = null)
    {
        $setting = self::find($key);
        if ($setting) {
             // Xử lý kiểu toggle trả về boolean
             if ($setting->type === 'toggle') {
                 return (bool) $setting->value;
             }
             return $setting->value;
        }
        return $default;
    }
}