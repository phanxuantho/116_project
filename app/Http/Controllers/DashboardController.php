<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\MonthlyAllowance;
use App\Models\SemesterAllowance;
use App\Models\AcademicResult;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tổng số sinh viên
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'Đang học')->count();

        // 2. Thống kê Trạng thái sinh viên (Pie Chart)
        $statusStats = Student::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        // 3. Thống kê Trạng thái cấp tiền (Doughnut Chart)
        $fundingStats = Student::select('funding_status', DB::raw('count(*) as total'))
            ->groupBy('funding_status')
            ->pluck('total', 'funding_status')->toArray();

        // 4. Thống kê Kết quả học tập (Bar Chart) - Lấy kỳ gần nhất
        // Phân loại: <2.0 (Yếu/Kém), 2.0-2.5 (TB), 2.5-3.2 (Khá), 3.2-3.6 (Giỏi), >3.6 (Xuất sắc)
        $latestSemester = Semester::orderBy('id', 'desc')->first();
        $academicStats = [
            'Yếu/Kém' => 0, 'Trung bình' => 0, 'Khá' => 0, 'Giỏi' => 0, 'Xuất sắc' => 0
        ];

        if ($latestSemester) {
            $scores = AcademicResult::where('semester_id', $latestSemester->id)->pluck('academic_score');
            foreach ($scores as $score) {
                if ($score < 2.0) $academicStats['Yếu/Kém']++;
                elseif ($score < 2.5) $academicStats['Trung bình']++;
                elseif ($score < 3.2) $academicStats['Khá']++;
                elseif ($score < 3.6) $academicStats['Giỏi']++;
                else $academicStats['Xuất sắc']++;
            }
        }

        // 5. Thống kê Giải ngân theo kỳ (Line/Bar Chart)
        // Gom nhóm tổng tiền từ 2 bảng Monthly và Semester theo semester_id
        $semesters = Semester::with('schoolYear')->orderBy('id', 'asc')->take(10)->get(); // Lấy 10 kỳ gần nhất
        $budgetLabels = [];
        $budgetData = [];

        foreach ($semesters as $sem) {
            $semName = "HK{$sem->semester_number} ({$sem->schoolYear->name})";
            
            $monthlySum = MonthlyAllowance::where('semester_id', $sem->id)->sum('amount');
            $semesterSum = SemesterAllowance::where('semester_id', $sem->id)->sum('amount');
            
            $budgetLabels[] = $semName;
            $budgetData[] = $monthlySum + $semesterSum;
        }

        return view('dashboard', compact(
            'totalStudents', 
            'activeStudents', 
            'statusStats', 
            'fundingStats', 
            'academicStats',
            'budgetLabels',
            'budgetData'
        ));
    }
}