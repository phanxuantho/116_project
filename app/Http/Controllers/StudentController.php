<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // Phương thức này xử lý việc hiển thị danh sách sinh viên và logic lọc
    public function index(Request $request)
    {
        // Bắt đầu một câu truy vấn Eloquent trên model Student.
        // with('class.major.faculty') thực hiện "eager loading" để tải trước các mối quan hệ,
        // giúp tránh vấn đề N+1 query và tăng hiệu suất.
        $query = Student::query()->with(['class.major.faculty']);

        // Lọc theo Khoa
        if ($request->filled('faculty_id')) {
            $query->whereHas('class.faculty', function ($q) use ($request) {
                $q->where('id', $request->faculty_id);
            });
        }

        // Lọc theo Ngành
        if ($request->filled('major_id')) {
            $query->whereHas('class.major', function ($q) use ($request) {
                $q->where('id', $request->major_id);
            });
        }
        
        // Lọc theo Khóa học (Lấy từ cột `course_year` của bảng `116_classes`)
        if ($request->filled('course_year')) {
            $query->whereHas('class', function ($q) use ($request) {
                $q->where('course_year', $request->course_year);
            });
        }

        // Lọc theo Lớp
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Lọc theo tình trạng học (sử dụng cột `status` từ CSDL)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Thực thi câu truy vấn, sắp xếp theo mã sinh viên, và phân trang (20 sinh viên mỗi trang).
        // withQueryString() giữ lại các tham số lọc trên URL khi chuyển trang.
        $students = $query->orderBy('student_code')->paginate(20)->withQueryString();

        // Lấy tất cả dữ liệu từ các bảng liên quan để điền vào các dropdown trong form lọc
        $faculties = Faculty::orderBy('faculty_name')->get();
        $majors = Major::orderBy('major_name')->get();
        $classes = ClassModel::orderBy('class_name')->get();
        
        // Lấy danh sách các khóa học duy nhất từ bảng 'classes'
        $courses = ClassModel::select('course_year')
                            ->distinct()
                            ->orderBy('course_year', 'desc')
                            ->get();

        // Trả về view 'students.index' và truyền các biến dữ liệu cần thiết.
        return view('students.index', [
            'students' => $students,
            'faculties' => $faculties,
            'majors' => $majors,
            'classes' => $classes,
            'courses' => $courses,
            'filters' => $request->all() // Giữ lại các giá trị đã lọc để hiển thị lại trên form
        ]);
    }
}
