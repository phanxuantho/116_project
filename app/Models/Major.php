<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;
    protected $table = '116_majors';
// KHAI BÁO CÁC CỘT ĐƯỢC PHÉP THÊM/SỬA
    protected $fillable = [
        'major_code',
        'major_name',
        'faculty_id',
    ];
    // Định nghĩa quan hệ: Một Ngành (Major) thuộc về một Khoa (Faculty)
    public function faculty() {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'id');
    }

    // Định nghĩa quan hệ: Một Ngành (Major) có nhiều Lớp (ClassModel)
    public function classes() {
        return $this->hasMany(ClassModel::class, 'major_id', 'id');
    }
}