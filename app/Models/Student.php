<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Province;
use App\Models\Ward;

class Student extends Model
{
    use HasFactory;
    // Chỉ định tên bảng sinh viên của bạn
    protected $table = '116_students'; 
    
    // Vì khóa chính của bạn là `student_code` chứ không phải `id`
    protected $primaryKey = 'student_code';

    // Vì `student_code` không phải là số tự tăng
    public $incrementing = false;
    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'gender',
        'dob',
        'citizen_id_card',
        'email',
        'phone',
        'class_id',
        'status',
        'funding_status', // <-- THÊM DÒNG NÀY
        'province_code',
        'ward_code',
        'address_detail',
        'old_address_detail',
        'bank_account',
        'bank_name',
        'bank_branch',
    ];

        /**
     * THÊM MỚI: Định nghĩa mối quan hệ để lấy lịch sử log.
     */
    public function statusLogs()
    {
        // Một sinh viên có nhiều bản ghi log, sắp xếp theo ngày tạo mới nhất
        return $this->hasMany(StudentStatusLog::class, 'student_code', 'student_code')->latest();
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
    // Một Sinh viên (Student) thuộc về một Lớp (ClassModel)
    public function class() {
        // Tham số thứ 2 ('class_id') là khóa ngoại trong bảng `116_students`
        // Tham số thứ 3 ('id') là khóa chính trong bảng `116_classes`
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }
    public function ward()
    {
        // 'ward_code' là khóa ngoại ở bảng students
        // 'code' là khóa chính ở bảng wards
        return $this->belongsTo(Ward::class, 'ward_code', 'code');
    }
    public function academicResults()
    {
        // 'student_code' là khóa ngoại ở bảng 116_academic_results
        // 'student_code' là khóa chính ở bảng 116_students
        return $this->hasMany(AcademicResult::class, 'student_code', 'student_code');
    }
    public function graduation()
    {
        // 'student_code' là khóa liên kết
        return $this->hasOne(Graduation::class, 'student_code', 'student_code');
    }
    public function employment()
    {
        // student_code ở bảng này liên kết với student_code ở bảng GraduateEmployment
        return $this->hasOne(GraduateEmployment::class, 'student_code', 'student_code');
    }
}