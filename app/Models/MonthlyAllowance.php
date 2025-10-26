<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyAllowance extends Model
{
    use HasFactory;

    protected $table = '116_monthly_allowances';

    protected $fillable = [
        'student_code',
        'school_year_id',
        'semester_id',
        'payment_month',
        'payment_year',
        'amount',
        'status',
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

    /**
     * Lấy năm học
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'id');
    }
}