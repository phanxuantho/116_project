<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStatusLog extends Model
{
    use HasFactory;

    protected $table = '116_student_status_logs';

    protected $fillable = [
        'student_code',
        'user_id',
        'status_old',
        'status_new',
        'funding_status_old',
        'funding_status_new',
        'note',
        'evidence',
        'evidence_date',
        'evidence_file_path',
    ];

    /**
     * Lấy thông tin sinh viên mà log này thuộc về.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }

    /**
     * Lấy thông tin người dùng đã thực hiện thay đổi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
