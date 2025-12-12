<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyAllowance;
use App\Models\SemesterAllowance;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class AllowanceController extends Controller
{
    // =========================================================================
    // PHẦN 1: CẤP PHÁT THEO THÁNG (Bảng 116_monthly_allowances)
    // =========================================================================

    public function createMonthly()
    {
        $classes = ClassModel::orderBy('class_name')->get();
        $schoolYears = SchoolYear::orderBy('id', 'desc')->get();
        $semesters = Semester::with('schoolYear')->orderBy('id', 'desc')->get();
        return view('allowances.monthly.create', compact('classes', 'schoolYears', 'semesters'));
    }

    /**
     * Bước 2: Xử lý logic lọc SV và hiển thị bản Nháp (Preview)
     */
    public function previewMonthly(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_month' => 'required|integer',
            'payment_year' => 'required|integer',
            'school_year_id' => 'required',
            'semester_id' => 'required',
            'class_ids' => 'required|array|min:1',
        ]);

        // Lấy danh sách sinh viên theo logic đặc biệt
        $students = $this->getStudentsByLogic($request->class_ids);

        // Đẩy dữ liệu sang View Preview để người dùng kiểm tra (Trạng thái Draft)
        return view('allowances.monthly.preview', [
            'students' => $students,
            'input' => $request->all(), // Truyền lại thông tin đã nhập
            'classes_count' => count($request->class_ids)
        ]);
    }

    /**
     * Bước 3: Xác nhận Duyệt và Lưu vào DB
     */
    public function storeMonthly(Request $request)
    {
        // Nhận dữ liệu đã được duyệt từ form Preview (được gửi qua hidden input)
        $data = json_decode($request->input('data'), true); // Mảng danh sách SV và số tiền
        $meta = json_decode($request->input('meta'), true); // Thông tin chung (tháng, năm...)

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($data as $item) {
                // Kiểm tra trùng lặp: Nếu SV đã được cấp tháng này rồi thì bỏ qua hoặc cập nhật
                // Ở đây ta chọn cách: Tạo mới (nếu muốn tránh trùng lặp phải check exists)
                
                MonthlyAllowance::create([
                    'student_code' => $item['student_code'],
                    'school_year_id' => $meta['school_year_id'],
                    'semester_id' => $meta['semester_id'],
                    'payment_month' => $meta['payment_month'],
                    'payment_year' => $meta['payment_year'],
                    'amount' => $meta['amount'],
                    'status' => 'Chưa chi trả', // Mặc định khi mới tạo
                    'note' => $meta['note'] ?? null,
                ]);
                $count++;
            }
            DB::commit();
            return redirect()->route('allowances.monthly.create')
                ->with('success', "Đã phê duyệt và cấp phát thành công cho $count sinh viên.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PHẦN 2: CẤP PHÁT THEO ĐỢT/KỲ (Bảng 116_semester_allowances)
    // =========================================================================

    public function createSemester()
    {
        $classes = ClassModel::orderBy('class_name')->get();
        $semesters = Semester::with('schoolYear')->orderBy('id', 'desc')->get();
        return view('allowances.semester.create', compact('classes', 'semesters'));
    }

    public function previewSemester(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'semester_id' => 'required',
            'installment_number' => 'required|integer',
            'months_covered' => 'required|numeric',
            'class_ids' => 'required|array|min:1',
        ]);

        $students = $this->getStudentsByLogic($request->class_ids);

        return view('allowances.semester.preview', [
            'students' => $students,
            'input' => $request->all(),
            'classes_count' => count($request->class_ids)
        ]);
    }

    public function storeSemester(Request $request)
    {
        $data = json_decode($request->input('data'), true);
        $meta = json_decode($request->input('meta'), true);

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($data as $item) {
                SemesterAllowance::create([
                    'student_code' => $item['student_code'],
                    'semester_id' => $meta['semester_id'],
                    'installment_number' => $meta['installment_number'],
                    'months_covered' => $meta['months_covered'],
                    'start_month' => $meta['start_month'] ?? null,
                    'amount' => $meta['amount'],
                    'status' => 'Chưa chi trả',
                    'note' => $meta['note'] ?? null,
                ]);
                $count++;
            }
            DB::commit();
            return redirect()->route('allowances.semester.create')
                ->with('success', "Đã phê duyệt và cấp phát theo đợt thành công cho $count sinh viên.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // LOGIC LỌC SINH VIÊN (Dùng chung)
    // =========================================================================
    
    private function getStudentsByLogic($classIds)
    {
        // Tách ID lớp thành 2 nhóm
        $activeClassIds = ClassModel::whereIn('id', $classIds)->where('class_status', 'Đang học')->pluck('id');
        $graduatedClassIds = ClassModel::whereIn('id', $classIds)->where('class_status', 'Đã tốt nghiệp')->pluck('id');

        $students = collect();

        // 1. Logic cho lớp Đang học
        // Chỉ lấy SV: Status = Đang học VÀ Funding = Đang nhận
        if ($activeClassIds->isNotEmpty()) {
            $activeStudents = Student::with('class')
                ->whereIn('class_id', $activeClassIds)
                ->where('status', 'Đang học')
                ->where('funding_status', 'Đang nhận')
                ->get();
            $students = $students->merge($activeStudents);
        }

        // 2. Logic cho lớp Đã tốt nghiệp
        // Chỉ lấy SV: Status = Gia hạn
        if ($graduatedClassIds->isNotEmpty()) {
            $graduatedStudents = Student::with('class')
                ->whereIn('class_id', $graduatedClassIds)
                ->where('status', 'Gia hạn')
                ->get();
            $students = $students->merge($graduatedStudents);
        }

        return $students->sortBy(function($st) {
            return $st->class_id . '-' . $st->full_name;
        });
    }
}