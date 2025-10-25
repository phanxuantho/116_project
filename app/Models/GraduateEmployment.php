<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Thêm import

class GraduateEmployment extends Model
{
    use HasFactory;

    protected $table = '116_graduate_employment'; // Chỉ định tên bảng

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_code',
        'employment_status',
        'job_title',
        'company_name',
        'company_address',
        'company_phone', // <-- THÊM DÒNG NÀY
        'employment_type',
        'start_date',
        'contract_type',
        'is_teaching_related',
        
        // --- CỘT CẬP NHẬT ---
        'teaching_province_code', // Mới (thay cho teaching_location)
        
        'contact_email',
        'contact_phone',
        
        // --- CÁC CỘT CẬP NHẬT (thay cho contact_address) ---
        'contact_province_code', 
        'contact_ward_code',
        'contact_address_detail',
        
        'notes',
        'declaration_date',
    ];

    /**
     * Lấy thông tin sinh viên.
     */
    public function student(): BelongsTo
    {
        // Quan hệ 1-1: Mỗi bản ghi việc làm thuộc về 1 sinh viên
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }

    /**
     * Lấy thông tin tỉnh/thành phố nơi công tác.
     */
    public function teachingProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'teaching_province_code', 'code');
    }

    /**
     * Lấy thông tin tỉnh/thành phố liên hệ.
     */
    public function contactProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'contact_province_code', 'code');
    }

    /**
     * Lấy thông tin xã/phường liên hệ.
     */
    public function contactWard(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'contact_ward_code', 'code');
    }
}