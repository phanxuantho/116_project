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
        // Lấy danh sách Khóa học (course_year) từ bảng Classes
        $courses = ClassModel::select('course_year')->distinct()->orderBy('course_year', 'desc')->pluck('course_year');
        
        // Lấy danh sách Lớp (nếu đã chọn Khóa thì chỉ hiện lớp của khóa đó)
        $classesQuery = ClassModel::orderBy('class_name');
        if ($request->course_year) {
            $classesQuery->where('course_year', $request->course_year);
        }
        $classes = $classesQuery->get();

        // 2. Xử lý Logic Lọc Dữ liệu
        $statusFilter = $request->input('status', 'Đã có việc làm'); // Mặc định
        
        // Query cơ bản lấy sinh viên ĐÃ TỐT NGHIỆP hoặc lớp ĐÃ TỐT NGHIỆP
        $query = Student::query()
            ->with(['class', 'employment', 'employment.teachingProvince'])
            ->whereHas('class', function($q) use ($request) {
                // Điều kiện lớp đã tốt nghiệp (quan trọng)
                $q->where('class_status', 'Đã tốt nghiệp');
                
                // Lọc theo khóa
                if ($request->course_year) {
                    $q->where('course_year', $request->course_year);
                }
                // Lọc theo lớp cụ thể
                if ($request->class_id) {
                    $q->where('id', $request->class_id);
                }
            });

        // Áp dụng bộ lọc Tình trạng
        if ($statusFilter === 'Chưa khai báo') {
            // Lấy SV không có bản ghi trong bảng việc làm
            $query->doesntHave('employment');
        } else {
            // Lấy SV CÓ bản ghi và khớp trạng thái
            $query->whereHas('employment', function($q) use ($statusFilter) {
                if ($statusFilter !== 'Tất cả') { // Nếu muốn xem hết thì bỏ qua where
                    $q->where('employment_status', $statusFilter);
                }
            });
        }

        $students = $query->paginate(20)->withQueryString();

        // 3. Thống kê tổng quan cho biểu đồ (Dựa trên bộ lọc khóa/lớp hiện tại)
        // Cần query riêng để đếm toàn bộ, không phân trang
        $statsQuery = Student::whereHas('class', function($q) use ($request) {
            $q->where('class_status', 'Đã tốt nghiệp');
            if ($request->course_year) $q->where('course_year', $request->course_year);
            if ($request->class_id) $q->where('id', $request->class_id);
        });
        
        $totalGraduates = $statsQuery->count();
        $declaredCount = (clone $statsQuery)->has('employment')->count();
        $notDeclaredCount = $totalGraduates - $declaredCount;
        
        // Thống kê chi tiết trạng thái việc làm
        $employmentStats = GraduateEmployment::select('employment_status', DB::raw('count(*) as total'))
            ->whereIn('student_code', (clone $statsQuery)->select('student_code'))
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status')
            ->toArray();
        
        // Merge thêm 'Chưa khai báo' vào mảng thống kê để vẽ biểu đồ
        $chartData = $employmentStats;
        $chartData['Chưa khai báo'] = $notDeclaredCount;

        return view('reports.graduate.employment', compact('students', 'courses', 'classes', 'chartData', 'totalGraduates', 'request'));
    }

    public function export(Request $request)
    {
        // Logic lọc y hệt hàm index nhưng dùng get() thay vì paginate()
        $statusFilter = $request->input('status', 'Đã có việc làm');
        
        $query = Student::query()
            ->with(['class', 'employment'])
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

        // --- XỬ LÝ EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
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
                // ...
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