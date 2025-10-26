<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionGrant extends Model
{
    use HasFactory;

    protected $table = '116_tuition_grants';

    protected $fillable = [
        'student_code',
        'semester_id',
        'registered_credits',
        'grant_amount',
        'paid_at',
        'note',
    ];

    /**
     * Lấy sinh viên của khoản hỗ trợ này
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }

    /**
     * Lấy học kỳ của khoản hỗ trợ này
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }
}