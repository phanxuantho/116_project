<?php
/*
|--------------------------------------------------------------------------
| FILE 2: app/Models/Ward.php
|--------------------------------------------------------------------------
|
| Model này tương tác với bảng `116_wards`.
|
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    // Chỉ định tên bảng
    protected $table = '116_wards';

    // Cấu hình khóa chính tương tự như Province Model
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Định nghĩa mối quan hệ "một xã/phường thuộc về một tỉnh".
     */
    public function province()
    {
        // Tham số thứ 2 là khóa ngoại trong bảng này ('116_wards')
        // Tham số thứ 3 là khóa chính của bảng liên quan ('116_provinces')
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}