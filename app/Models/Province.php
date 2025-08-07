<?php

/*
|--------------------------------------------------------------------------
| FILE 1: app/Models/Province.php
|--------------------------------------------------------------------------
|
| Model này tương tác với bảng `116_provinces`.
|
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    // Chỉ định tên bảng
    protected $table = '116_provinces';

    // Vì khóa chính ('code') không phải là số và không tự tăng,
    // chúng ta cần cấu hình cho Eloquent biết điều đó.
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Định nghĩa mối quan hệ "một tỉnh có nhiều xã/phường".
     */
    public function wards()
    {
        // Tham số thứ 2 là khóa ngoại trong bảng '116_wards'
        // Tham số thứ 3 là khóa chính trong bảng '116_provinces'
        return $this->hasMany(Ward::class, 'province_code', 'code');
    }
}