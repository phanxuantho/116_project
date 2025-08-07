<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\Province; // Giả sử bạn đã tạo Model cho Tỉnh
use App\Models\Ward;     // Giả sử bạn đã tạo Model cho Xã/Phường
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

     /**
     * THÊM PHƯƠNG THỨC NÀY: Hiển thị form để chỉnh sửa thông tin sinh viên.
     * Laravel sẽ tự động tìm sinh viên dựa trên {student} (mã SV) trong URL.
     */
    public function edit(Student $student)
    {
        $classes = ClassModel::orderBy('class_name')->get();
        // Lấy danh sách tỉnh/thành phố để điền vào dropdown
        $provinces = DB::table('116_provinces')->orderBy('name')->get();

        return view('students.edit', compact('student', 'classes', 'provinces'));
    }

    /**
     * Cập nhật thông tin sinh viên trong CSDL.
     */
    public function update(Request $request, Student $student)
    {
        // Xác thực dữ liệu đầu vào với đầy đủ các trường
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:100',
            'gender' => 'nullable|in:Nam,Nữ,Khác',
            'dob' => 'required|date',
            'citizen_id_card' => ['required', 'string', 'max:12', Rule::unique('116_students')->ignore($student->student_code, 'student_code')],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('116_students')->ignore($student->student_code, 'student_code')],
            'phone' => 'nullable|string|max:15',
            'class_id' => 'required|exists:116_classes,id',
            'status' => 'required|in:Đang học,Bảo lưu,Tốt nghiệp,Thôi học',
            'province_code' => 'nullable|exists:116_provinces,code',
            'ward_code' => 'nullable|exists:116_wards,code',
            'address_detail' => 'nullable|string',
            'old_address_detail' => 'nullable|string',
            'bank_account' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
        ]);

        // Cập nhật thông tin sinh viên
        $student->update($validatedData);

        // Chuyển hướng về trang danh sách với thông báo thành công
        return redirect()->route('students.index')->with('success', 'Cập nhật thông tin sinh viên thành công!');
    }

    /**
     * PHƯƠNG THỨC MỚI: Lấy danh sách xã/phường dựa trên mã tỉnh.
     */
    public function getWards(Request $request)
    {
        $wards = DB::table('116_wards')
                    ->where('province_code', $request->province_code)
                    ->orderBy('name')
                    ->get();
        return response()->json($wards);
    }






}