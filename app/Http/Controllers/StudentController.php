<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\StudentStatusLog; // Thêm dòng này
use Illuminate\Support\Facades\Auth; // Thêm dòng này
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
        // THÊM MỚI: Xử lý tìm kiếm
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('student_code', 'LIKE', '%' . $searchTerm . '%');
            });
        }
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
        // Tải trước mối quan hệ statusLogs cùng với thông tin người dùng (user) đã thay đổi
        $student->load('statusLogs.user');
        $classes = ClassModel::orderBy('class_name')->get();
        // Lấy danh sách tỉnh/thành phố để điền vào dropdown
        $provinces = DB::table('116_provinces')->orderBy('name')->get();
        // Lấy chuỗi query từ URL của trang danh sách (trang trước đó)
        $previousUrlQuery = parse_url(url()->previous(), PHP_URL_QUERY);
        return view('students.edit', compact('student', 'classes', 'provinces', 'previousUrlQuery'));




    }

    /**
     * Cập nhật thông tin sinh viên trong CSDL.
     */
    public function update(Request $request, Student $student)
    {
        // Lấy trạng thái cũ TRƯỚC KHI cập nhật
        $oldStatus = $student->status;
        $oldFundingStatus = $student->funding_status;

        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:100',
            'gender' => 'nullable|in:Nam,Nữ,Khác',
            'dob' => 'required|date',
            'citizen_id_card' => ['required', 'string', 'max:12', Rule::unique('116_students')->ignore($student->student_code, 'student_code')],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('116_students')->ignore($student->student_code, 'student_code')],
            'phone' => 'nullable|string|max:15',
            'class_id' => 'required|exists:116_classes,id',
            'status' => 'required|in:Đang học,Bảo lưu,Tốt nghiệp,Thôi học',
            'funding_status' => 'required|in:Đang nhận,Tạm dừng nhận,Thôi nhận',
            'province_code' => 'nullable|exists:116_provinces,code',
            'ward_code' => 'nullable|exists:116_wards,code',
            'address_detail' => 'nullable|string',
            'old_address_detail' => 'nullable|string',
            'bank_account' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            // Validation cho các trường log
            'note' => 'nullable|string',
            'evidence' => 'nullable|string|max:150',
            'evidence_date' => 'nullable|date',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Giới hạn file 2MB
        ]);

        // Cập nhật thông tin sinh viên
        $student->update($validatedData);

        // Kiểm tra xem trạng thái có thay đổi không
        if ($oldStatus !== $request->status || $oldFundingStatus !== $request->funding_status) {
            $filePath = null;
            // Xử lý upload file minh chứng nếu có
            // Xử lý upload và đổi tên file minh chứng nếu có
            if ($request->hasFile('evidence_file')) {
                $file = $request->file('evidence_file');
                // Lấy tên gốc của file
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // Lấy đuôi file
                $extension = $file->getClientOriginalExtension();
                // Tạo timestamp
                $timestamp = now()->format('Ymd_His');
                // Tạo tên file mới theo định dạng yêu cầu
                $newFileName = $student->student_code . '_' . $timestamp . '_' . \Str::slug($originalName) . '.' . $extension;
                
                // Lưu file với tên mới vào thư mục `storage/app/public/evidence_files`
                $filePath = $file->storeAs('evidence_files', $newFileName, 'public');
            }

            // Tạo bản ghi log
            StudentStatusLog::create([
                'student_code' => $student->student_code,
                'user_id' => Auth::id(), // Lấy ID của người dùng đang đăng nhập
                'status_old' => $oldStatus,
                'status_new' => $request->status,
                'funding_status_old' => $oldFundingStatus,
                'funding_status_new' => $request->funding_status,
                'note' => $request->note,
                'evidence' => $request->evidence,
                'evidence_date' => $request->evidence_date,
                'evidence_file_path' => $filePath,
            ]);
        }
       // Xây dựng URL chuyển hướng có kèm theo các tham số lọc cũ
       $redirectUrl = route('students.index');
       if ($request->filled('previous_url_query')) {
           $redirectUrl .= '?' . $request->previous_url_query;
       }
       return redirect($redirectUrl)->with('success', 'Cập nhật thông tin sinh viên thành công!');
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