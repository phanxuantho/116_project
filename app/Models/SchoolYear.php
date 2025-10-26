<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    use HasFactory;

    protected $table = '116_school_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    /**
     * Lấy các học kỳ thuộc năm học
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'school_year_id', 'id');
    }
}