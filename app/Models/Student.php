<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    // Chỉ định tên bảng sinh viên của bạn
    protected $table = '116_students'; 
    
    // Vì khóa chính của bạn là `student_code` chứ không phải `id`
    protected $primaryKey = 'student_code';

    // Vì `student_code` không phải là số tự tăng
    public $incrementing = false;

    // Một Sinh viên (Student) thuộc về một Lớp (ClassModel)
    public function class() {
        // Tham số thứ 2 ('class_id') là khóa ngoại trong bảng `116_students`
        // Tham số thứ 3 ('id') là khóa chính trong bảng `116_classes`
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }
}