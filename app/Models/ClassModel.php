<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Đặt tên là ClassModel để tránh xung đột với từ khóa 'class' của PHP
class ClassModel extends Model
{
    use HasFactory;
    protected $table = '116_classes';

    // Một Lớp (ClassModel) thuộc về một Ngành (Major)
    public function major() {
        return $this->belongsTo(Major::class, 'major_id', 'id');
    }

    // Một Lớp (ClassModel) có nhiều Sinh viên (Student)
    public function students() {
        return $this->hasMany(Student::class, 'class_id', 'id');
    }
    
    // Một Lớp (ClassModel) thuộc về một Khoa (Faculty)
    public function faculty() {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'id');
    }
}