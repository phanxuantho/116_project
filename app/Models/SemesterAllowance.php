<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterAllowance extends Model
{
    use HasFactory;

    protected $table = '116_semester_allowances';

    protected $fillable = [
        'student_code',
        'semester_id',
        'installment_number', // Đợt cấp phát
        'months_covered',     // Số tháng (vd: 1.5)
        'start_month',        // Tháng bắt đầu
        'amount',
        'status',             // Trạng thái
        'paid_at',
        'note',
    ];

    /**
     * Lấy sinh viên
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }

    /**
     * Lấy học kỳ
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }
}