<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graduation extends Model
{
    use HasFactory;

    // !!! THIS IS THE FIX !!!
    protected $table = '116_graduations'; 

    protected $fillable = [
        'student_code',
        'gpa_final',
        'graduation_rank',
        'conduct_score',
        'conduct_rank',
        'training_time',
        'decision_number',
        'degree_number',
        // Add other fields if necessary
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }
}