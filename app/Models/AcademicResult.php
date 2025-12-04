<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicResult extends Model
{
    use HasFactory;

    protected $table = '116_academic_results';

    protected $fillable = [
        'student_code',
        'semester_id',
        'academic_score',
        'conduct_score',
        'registered_credits',
        'accumulated_credits',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }
}