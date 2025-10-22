<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduateEmployment extends Model
{
    use HasFactory;

    protected $table = '116_graduate_employment'; // Chỉ định tên bảng

    protected $fillable = [ // Các cột được phép gán hàng loạt
        'student_code',
        'employment_status',
        'job_title',
        'company_name',
        'company_address',
        'employment_type',
        'start_date',
        'contract_type',
        'is_teaching_related',
        'teaching_location',
        'contact_email',
        'contact_phone',
        'contact_address',
        'notes',
        'declaration_date',
    ];

    // Định nghĩa mối quan hệ với model Student
    public function student()
    {
        // Quan hệ 1-1: Mỗi bản ghi việc làm thuộc về 1 sinh viên
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }

    // Các accessor, mutator hoặc scope khác nếu cần
}