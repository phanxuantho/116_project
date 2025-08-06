<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // Phương thức này xử lý việc hiển thị danh sách sinh viên và logic lọc chính
    public function index(Request $request)
    {
        // Bắt đầu một câu truy vấn Eloquent trên model Student.
        // with('class.major.faculty') thực hiện "eager loading" để tải trước các mối quan hệ,
        // giúp tránh vấn đề N+1 query và tăng hiệu suất.
        $query = Student::query()->with('class.major.faculty');

        // Lọc theo Khoa
        if ($request->filled('faculty_id')) {
            $query->whereHas('class.faculty', function ($q) use ($request) {
                $q->where('id', $request->faculty_id);
            });
        }
        
        // Lọc theo Khóa học (dựa trên cột course_year của bảng classes)
        if ($request->filled('course_year')) {
            $query->whereHas('class', function ($q) use ($request) {
                $q->where('course_year', $request->course_year);
            });
        }

        // Lọc theo Lớp
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Lọc theo Tình trạng học tập (sử dụng cột 'status' trong bảng students)
        if ($request->filled('academic_status')) {
            $query->where('status', $request->academic_status);
        }

        // Thực thi câu truy vấn, sắp xếp theo mã sinh viên, và phân trang (20 sinh viên mỗi trang).
        // withQueryString() giữ lại các tham số lọc trên URL khi chuyển trang.
        $students = $query->orderBy('student_code')->paginate(20)->withQueryString();

        // Lấy tất cả dữ liệu cho các dropdown bộ lọc
        $faculties = Faculty::orderBy('faculty_name')->get();
        $classes = ClassModel::orderBy('class_name')->get();
        
        // Lấy danh sách các khóa học duy nhất từ bảng 'classes'
        $courses = ClassModel::select('course_year')
                            ->distinct()
                            ->orderBy('course_year', 'desc')
                            ->get();

        // Trả về view cùng với dữ liệu
        return view('students.index', [
            'students' => $students,
            'faculties' => $faculties,
            'classes' => $classes,
            'courses' => $courses,
            'filters' => $request->all() // Giữ lại các giá trị đã lọc để hiển thị lại trên form
        ]);
    }

    /**
     * PHƯƠNG THỨC MỚI: Lấy danh sách lớp học dựa trên bộ lọc (Khoa/Khóa).
     * Phương thức này sẽ được gọi bằng JavaScript (AJAX/Fetch) để cập nhật dropdown Lớp.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClasses(Request $request)
    {
        // Bắt đầu câu truy vấn trên model ClassModel
        $query = ClassModel::query();

        // Nếu có faculty_id được gửi lên, thêm điều kiện lọc theo khoa
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        // Nếu có course_year được gửi lên, thêm điều kiện lọc theo khóa học
        if ($request->filled('course_year')) {
            $query->where('course_year', $request->course_year);
        }

        // Lấy danh sách lớp đã lọc và sắp xếp theo tên
        $classes = $query->orderBy('class_name')->get();

        // Trả về kết quả dưới dạng JSON
        return response()->json($classes);
    }
}