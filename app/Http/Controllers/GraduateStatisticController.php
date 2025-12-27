<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\GraduateEmployment;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GraduateStatisticController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dữ liệu cho bộ lọc
        $courses = ClassModel::select('course_year')->distinct()->orderBy('course_year', 'desc')->pluck('course_year');
        
        $classesQuery = ClassModel::orderBy('class_name');
        if ($request->course_year) {
            $classesQuery->where('course_year', $request->course_year);
        }
        $classes = $classesQuery->get();

        // 2. Xử lý Logic Lọc Dữ liệu
        $statusFilter = $request->input('status', 'Đã có việc làm'); 
        
        // --- CẬP NHẬT 1: Thêm điều kiện status = 'Tốt nghiệp' ---
        $query = Student::query()
            ->with(['class', 'employment', 'employment.teachingProvince'])
            ->where('status', 'Tốt nghiệp') // <--- CHỈ LẤY SV ĐÃ TỐT NGHIỆP
            ->whereHas('class', function($q) use ($request) {
                // Vẫn giữ điều kiện lớp đã tốt nghiệp
                $q->where('class_status', 'Đã tốt nghiệp');
                
                if ($request->course_year) {
                    $q->where('course_year', $request->course_year);
                }
                if ($request->class_id) {
                    $q->where('id', $request->class_id);
                }
            });

        // Áp dụng bộ lọc Tình trạng việc làm
        if ($statusFilter === 'Chưa khai báo') {
            $query->doesntHave('employment');
        } else {
            $query->whereHas('employment', function($q) use ($statusFilter) {
                if ($statusFilter !== 'Tất cả') {
                    $q->where('employment_status', $statusFilter);
                }
            });
        }

        $students = $query->paginate(20)->withQueryString();

        // 3. Thống kê tổng quan cho biểu đồ
        // --- CẬP NHẬT 2: Thêm điều kiện status = 'Tốt nghiệp' cho query thống kê ---
        $statsQuery = Student::query()
            ->where('status', 'Tốt nghiệp') // <--- QUAN TRỌNG: Để đếm đúng tổng số
            ->whereHas('class', function($q) use ($request) {
                $q->where('class_status', 'Đã tốt nghiệp');
                if ($request->course_year) $q->where('course_year', $request->course_year);
                if ($request->class_id) $q->where('id', $request->class_id);
            });
        
        $totalGraduates = $statsQuery->count();
        $declaredCount = (clone $statsQuery)->has('employment')->count();
        $notDeclaredCount = $totalGraduates - $declaredCount;
        
        $employmentStats = GraduateEmployment::select('employment_status', DB::raw('count(*) as total'))
            ->whereIn('student_code', (clone $statsQuery)->select('student_code'))
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status')
            ->toArray();
        
        $chartData = $employmentStats;
        $chartData['Chưa khai báo'] = $notDeclaredCount;

        return view('reports.graduate.employment', compact('students', 'courses', 'classes', 'chartData', 'totalGraduates', 'request'));
    }

    public function export(Request $request)
    {
        $statusFilter = $request->input('status', 'Đã có việc làm');
        
        // --- CẬP NHẬT 3: Thêm điều kiện status = 'Tốt nghiệp' cho query Excel ---
        $query = Student::query()
            ->with(['class', 'employment'])
            ->where('status', 'Tốt nghiệp') // <--- LỌC SV ĐÃ TỐT NGHIỆP
            ->whereHas('class', function($q) use ($request) {
                $q->where('class_status', 'Đã tốt nghiệp');
                if ($request->course_year) $q->where('course_year', $request->course_year);
                if ($request->class_id) $q->where('id', $request->class_id);
            });

        if ($statusFilter === 'Chưa khai báo') {
            $query->doesntHave('employment');
        } else {
            $query->whereHas('employment', function($q) use ($statusFilter) {
                if ($statusFilter !== 'Tất cả') $q->where('employment_status', $statusFilter);
            });
        }
        
        $students = $query->get();

        // --- XỬ LÝ EXCEL (Giữ nguyên phần dưới) ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['STT', 'MSSV', 'Họ tên', 'Lớp', 'Tình trạng', 'Nơi làm việc', 'Vị trí', 'Loại hình', 'SĐT Liên hệ'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $row = 2;
        foreach ($students as $index => $st) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $st->student_code);
            $sheet->setCellValue('C' . $row, $st->full_name);
            $sheet->setCellValue('D' . $row, $st->class->class_name ?? '');
            
            if ($st->employment) {
                $sheet->setCellValue('E' . $row, $st->employment->employment_status);
                $sheet->setCellValue('F' . $row, $st->employment->company_name ?? 'N/A');
                $sheet->setCellValue('G' . $row, $st->employment->job_title ?? '');
                $sheet->setCellValue('H' . $row, $st->employment->employment_type ?? '');
                $sheet->setCellValue('I' . $row, $st->employment->contact_phone ?? '');
            } else {
                $sheet->setCellValue('E' . $row, 'Chưa khai báo');
                $sheet->setCellValue('F' . $row, '-');
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="thong-ke-viec-lam.xlsx"');
        $writer->save('php://output');
        exit;
    }
}