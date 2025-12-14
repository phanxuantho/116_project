<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

// Thư viện Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AcademicWarningReportController extends Controller
{
    /**
     * Hiển thị Form bộ lọc và Danh sách (nếu đã lọc)
     */
    public function index(Request $request)
    {
        // 1. Chuẩn bị dữ liệu cho bộ lọc
        $semesters = Semester::with('schoolYear')->orderBy('id', 'desc')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();
        
        // Load danh sách lớp (nếu có chọn khoa)
        $classes = collect();
        if ($request->faculty_id) {
            $classes = ClassModel::where('faculty_id', $request->faculty_id)->orderBy('class_name')->get();
        }

        $statuses = ['Đang học', 'Bảo lưu', 'Tốt nghiệp', 'Thôi học'];

        // 2. Xử lý tìm kiếm (nếu có request)
        $students = collect();
        $selectedSemester = null;

        // Mặc định chọn kỳ mới nhất nếu chưa chọn
        if (!$request->has('semester_id') && $semesters->isNotEmpty()) {
            $request->merge(['semester_id' => $semesters->first()->id]);
        }

        if ($request->semester_id) {
            $selectedSemester = Semester::find($request->semester_id);
            $students = $this->getReportData($request);
        }

        return view('reports.academic_warning.index', compact(
            'semesters', 'faculties', 'classes', 'statuses', 'students', 'selectedSemester'
        ));
    }

    /**
     * Xử lý In Báo cáo
     */
    public function print(Request $request)
    {
        $request->validate(['semester_id' => 'required']);
        
        $data = $this->getReportData($request); // Tự động xử lý scope 'all' trong hàm này
        $meta = $this->getReportMeta($request);

        return view('reports.academic_warning.print', compact('data', 'meta'));
    }

    /**
     * Xuất Excel (PhpSpreadsheet)
     */
    public function export(Request $request)
    {
        $request->validate(['semester_id' => 'required']);
        
        $data = $this->getReportData($request);
        $meta = $this->getReportMeta($request);

        // --- XỬ LÝ EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(11);

        // Định dạng cột
        $sheet->getColumnDimension('A')->setWidth(5);  // STT
        $sheet->getColumnDimension('B')->setWidth(15); // MSSV
        $sheet->getColumnDimension('C')->setWidth(25); // Họ tên
        $sheet->getColumnDimension('D')->setWidth(12); // Ngày sinh
        $sheet->getColumnDimension('E')->setWidth(30); // Lớp
        $sheet->getColumnDimension('F')->setWidth(12); // TC ĐK
        $sheet->getColumnDimension('G')->setWidth(12); // TC TL
        $sheet->getColumnDimension('H')->setWidth(10); // Điểm
        $sheet->getColumnDimension('I')->setWidth(15); // Xếp loại
        $sheet->getColumnDimension('J')->setWidth(12); // Điểm RL

        $currentRow = 1;

        // Gom nhóm theo Lớp để xuất
        $groupedData = $data->groupBy('class.class_name');

        foreach ($groupedData as $className => $students) {
            // Header Chung
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", 'TRƯỜNG ĐẠI HỌC TÂY NGUYÊN');
            $sheet->mergeCells("E{$currentRow}:J{$currentRow}")->setCellValue("E{$currentRow}", 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("E{$currentRow}:J{$currentRow}")->setCellValue("E{$currentRow}", 'Độc lập - Tự do - Hạnh phúc');
            $sheet->getStyle("E{$currentRow}:J{$currentRow}")->getFont()->setBold(true)->setUnderline(true);
            $sheet->getStyle("E{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow += 2;

            // Tiêu đề
            $title = "DANH SÁCH SINH VIÊN BỊ CẢNH BÁO HỌC TẬP - HỌC KỲ {$meta['semester']} ({$meta['year']})";
            $sheet->mergeCells("A{$currentRow}:J{$currentRow}")->setCellValue("A{$currentRow}", $title);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14)->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:J{$currentRow}")->setCellValue("A{$currentRow}", "Lớp: " . $className);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setItalic(true);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow += 2;

            // Header Bảng
            $tableStartRow = $currentRow;
            $headers = ['STT', 'MSSV', 'Họ và tên', 'Ngày sinh', 'Lớp', 'TC ĐK', 'TC TL', 'Điểm TB', 'Xếp loại', 'Điểm RL'];
            $col = 'A';
            foreach($headers as $header) {
                $sheet->setCellValue("{$col}{$currentRow}", $header);
                $col++;
            }
            
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');
            $currentRow++;

            // Dữ liệu
            $stt = 1;
            foreach ($students as $student) {
                $result = $student->academicResults->first();
                $score = $result ? $result->academic_score : 0;
                $rank = ($score < 1.0) ? 'Kém' : 'Yếu';

                $sheet->setCellValue("A{$currentRow}", $stt++);
                $sheet->setCellValueExplicit("B{$currentRow}", $student->student_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("C{$currentRow}", $student->full_name);
                $sheet->setCellValue("D{$currentRow}", \Carbon\Carbon::parse($student->dob)->format('d/m/Y'));
                $sheet->setCellValue("E{$currentRow}", $className);
                $sheet->setCellValue("F{$currentRow}", $result ? $result->registered_credits : '');
                $sheet->setCellValue("G{$currentRow}", $result ? $result->accumulated_credits : '');
                $sheet->setCellValue("H{$currentRow}", $score);
                $sheet->setCellValue("I{$currentRow}", $rank);
                $sheet->setCellValue("J{$currentRow}", $result ? $result->conduct_score : '');

                // Tô đỏ nếu xếp loại Kém
                if ($score < 1.0) {
                    $sheet->getStyle("I{$currentRow}")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                    $sheet->getStyle("I{$currentRow}")->getFont()->setBold(true);
                }

                $currentRow++;
            }

            // Kẻ khung
            $sheet->getStyle("A{$tableStartRow}:J" . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$tableStartRow}:J" . ($currentRow - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$tableStartRow}:B" . ($currentRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$tableStartRow}:J" . ($currentRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Ngắt trang sau mỗi lớp
            $sheet->setBreak("A{$currentRow}", Worksheet::BREAK_ROW);
            $currentRow += 2;
        }

        $fileName = 'Canh_bao_hoc_tap_HK' . $meta['semester'] . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Logic lấy dữ liệu cốt lõi
     */
    private function getReportData(Request $request)
    {
        $semesterId = $request->semester_id;
        $facultyId = $request->faculty_id;
        $classId = $request->class_id;
        $status = $request->status;
        
        // Kiểm tra xem người dùng muốn xuất "Tất cả" hay "Theo lọc"
        // Nếu nút bấm gửi param 'scope=all' thì bỏ qua lọc Lớp và Khoa
        $isScopeAll = $request->input('scope') === 'all';

        $query = Student::query()
            ->with(['class', 'academicResults' => function($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            }])
            // Chỉ lấy SV có điểm < 2.0 trong kỳ đó
            ->whereHas('academicResults', function($q) use ($semesterId) {
                $q->where('semester_id', $semesterId)
                  ->where('academic_score', '<', 2.0);
            });

        // 1. Lọc theo Khoa (Nếu không chọn In Tất Cả)
        if ($facultyId && !$isScopeAll) {
            $query->whereHas('class', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        // 2. Lọc theo Lớp (Nếu không chọn In Tất Cả)
        if ($classId && !$isScopeAll) {
            $query->where('class_id', $classId);
        }

        // 3. Lọc theo Trạng thái (Luôn áp dụng nếu có chọn)
        if ($status) {
            $query->where('status', $status);
        }

        // Sắp xếp: Khoa -> Lớp -> Tên
        return $query->get()
            ->sortBy(function($student) {
                return $student->class->class_name . $student->full_name;
            });
    }

    private function getReportMeta(Request $request)
    {
        $semester = Semester::with('schoolYear')->find($request->semester_id);
        return [
            'semester' => $semester->semester_number,
            'year' => $semester->schoolYear->name ?? '',
        ];
    }
}