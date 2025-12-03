<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;
    // Chỉ định tên bảng trong CSDL mà model này sẽ làm việc
    protected $table = '116_faculties';
    protected $fillable = [
        'faculty_code',
        'faculty_name',
    ];

    // Định nghĩa quan hệ: Một Khoa (Faculty) có nhiều Ngành (Major)
    public function majors() {
        return $this->hasMany(Major::class, 'faculty_id', 'id');
    }

    // Định nghĩa quan hệ: Một Khoa (Faculty) có nhiều Lớp (ClassModel)
    public function classes() {
        return $this->hasMany(ClassModel::class, 'faculty_id', 'id');
    }
}