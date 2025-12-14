<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyAllowance;
use App\Models\SemesterAllowance;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\Faculty;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class AllowanceStatisticController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dữ liệu bộ lọc
        $schoolYears = SchoolYear::orderBy('id', 'desc')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();
        $semesters = Semester::with('schoolYear')->orderBy('id', 'desc')->get();
        
        $classes = collect();
        if ($request->faculty_id) {
            $classes = ClassModel::where('faculty_id', $request->faculty_id)->get();
        } else {
            $classes = ClassModel::orderBy('class_name')->get(); // Mặc định lấy hết nếu ko chọn khoa
        }

        // 2. Query Thống kê Hàng tháng (Monthly)
        $monthlyQuery = DB::table('116_monthly_allowances as m')
            ->join('116_students as s', 'm.student_code', '=', 's.student_code')
            ->join('116_classes as c', 's.class_id', '=', 'c.id')
            ->join('116_faculties as f', 'c.faculty_id', '=', 'f.id')
            ->select(
                'c.id as class_id',
                'c.class_name',
                'f.faculty_name',
                'm.payment_month',
                'm.payment_year',
                DB::raw('"monthly" as type'), // Đánh dấu loại
                DB::raw('COUNT(m.id) as total_students'),
                DB::raw('SUM(m.amount) as total_amount')
            )
            ->groupBy('c.id', 'c.class_name', 'f.faculty_name', 'm.payment_month', 'm.payment_year');

        // 3. Query Thống kê Theo Kỳ (Semester)
        $semesterQuery = DB::table('116_semester_allowances as sem_al')
            ->join('116_students as s', 'sem_al.student_code', '=', 's.student_code')
            ->join('116_classes as c', 's.class_id', '=', 'c.id')
            ->join('116_faculties as f', 'c.faculty_id', '=', 'f.id')
            ->join('116_semesters as sem_def', 'sem_al.semester_id', '=', 'sem_def.id') // Join bảng định nghĩa kỳ để lấy tên
            ->join('116_school_years as sy', 'sem_def.school_year_id', '=', 'sy.id')
            ->select(
                'c.id as class_id',
                'c.class_name',
                'f.faculty_name',
                'sem_al.semester_id',
                'sem_al.installment_number',
                'sem_def.semester_number',
                'sy.name as school_year_name',
                DB::raw('"semester" as type'), // Đánh dấu loại
                DB::raw('COUNT(sem_al.id) as total_students'),
                DB::raw('SUM(sem_al.amount) as total_amount')
            )
            ->groupBy('c.id', 'c.class_name', 'f.faculty_name', 'sem_al.semester_id', 'sem_al.installment_number', 'sem_def.semester_number', 'sy.name');

        // --- ÁP DỤNG BỘ LỌC ---
        if ($request->school_year_id) {
            $monthlyQuery->where('m.school_year_id', $request->school_year_id);
            // Với bảng semester, ta filter dựa trên bảng semester_id -> school_year_id
            $semesterQuery->where('sem_def.school_year_id', $request->school_year_id);
        }
        if ($request->semester_id) {
            $monthlyQuery->where('m.semester_id', $request->semester_id);
            $semesterQuery->where('sem_al.semester_id', $request->semester_id);
        }
        if ($request->faculty_id) {
            $monthlyQuery->where('c.faculty_id', $request->faculty_id);
            $semesterQuery->where('c.faculty_id', $request->faculty_id);
        }
        if ($request->class_id) {
            $monthlyQuery->where('c.id', $request->class_id);
            $semesterQuery->where('c.id', $request->class_id);
        }

        // Lấy dữ liệu và gộp
        $monthlyData = $monthlyQuery->get();
        $semesterData = $semesterQuery->get();

        // Gộp 2 collection lại thành 1 danh sách duy nhất
        $statistics = $monthlyData->concat($semesterData);

        return view('allowances.statistics.index', compact('schoolYears', 'faculties', 'semesters', 'classes', 'statistics'));
    }

    /**
     * Xem chi tiết danh sách sinh viên
     */
    public function show(Request $request)
    {
        $type = $request->type;
        $classId = $request->class_id;
        
        $className = ClassModel::find($classId)->class_name ?? 'Lớp không tồn tại';
        $title = "";
        $students = collect();

        if ($type == 'monthly') {
            $month = $request->month;
            $year = $request->year;
            $title = "Danh sách chi trả SHP Tháng $month/$year - Lớp: $className";

            $students = MonthlyAllowance::with('student')
                ->whereHas('student', function($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->where('payment_month', $month)
                ->where('payment_year', $year)
                ->get();

        } elseif ($type == 'semester') {
            $semesterId = $request->semester_id;
            $installment = $request->installment;
            $semName = Semester::with('schoolYear')->find($semesterId);
            $semText = $semName ? "HK{$semName->semester_number} ({$semName->schoolYear->name})" : "";
            
            $title = "Danh sách chi trả $semText - Đợt $installment - Lớp: $className";

            $students = SemesterAllowance::with('student')
                ->whereHas('student', function($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->where('semester_id', $semesterId)
                ->where('installment_number', $installment)
                ->get();
        }

        return view('allowances.statistics.show', compact('students', 'title'));
    }
}