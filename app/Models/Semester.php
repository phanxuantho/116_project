<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    use HasFactory;

    protected $table = '116_semesters';

    protected $fillable = [
        'school_year_id',
        'semester_number',
        'name',
        'start_date',
        'end_date',
    ];

    /**
     * Lấy năm học của học kỳ này
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'id');
    }
}