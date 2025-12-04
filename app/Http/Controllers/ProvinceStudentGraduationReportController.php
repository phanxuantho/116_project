<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

// Thư viện Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProvinceStudentGraduationReportController extends Controller
{
    public function index()
    {
        // Lấy danh sách Khóa
        $courseYears = ClassModel::distinct()->orderBy('course_year', 'desc')->pluck('course_year');
        
        // Lấy danh sách Tỉnh
        $provinces = Province::orderBy('name')->get();

        // Lấy danh sách Số quyết định tốt nghiệp để lọc đợt
        $decisionNumbers = DB::table('116_graduations')
            ->select('decision_number')
            ->distinct()
            ->orderBy('decision_number', 'desc')
            ->pluck('decision_number');

        return view('reports.province_graduations.form', compact('courseYears', 'provinces', 'decisionNumbers'));
    }

    public function print(Request $request)
    {
        $request->validate(['course_year' => 'required']);
        
        $data = $this->getReportData($request);
        $meta = [
            'year' => $request->course_year,
            'decision' => $request->decision_number ?? 'Tất cả'
        ];

        return view('reports.province_graduations.print', compact('data', 'meta'));
    }

    public function export(Request $request)
    {
        $request->validate(['course_year' => 'required']);
        
        $data = $this->getReportData($request);
        $year = $request->course_year;

        // --- XUẤT EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(11);

        // Chiều rộng cột
        $sheet->getColumnDimension('A')->setWidth(5);  // STT
        $sheet->getColumnDimension('B')->setWidth(25); // Họ tên
        $sheet->getColumnDimension('C')->setWidth(12); // Ngày sinh
        $sheet->getColumnDimension('D')->setWidth(15); // MSSV
        $sheet->getColumnDimension('E')->setWidth(25); // Chuyên ngành
        $sheet->getColumnDimension('F')->setWidth(10); // ĐTB
        $sheet->getColumnDimension('G')->setWidth(10); // Xếp hạng
        $sheet->getColumnDimension('H')->setWidth(10); // ĐRL
        $sheet->getColumnDimension('I')->setWidth(10); // XL RL
        $sheet->getColumnDimension('J')->setWidth(15); // Thời gian ĐT
        $sheet->getColumnDimension('K')->setWidth(15); // QĐ số
        $sheet->getColumnDimension('L')->setWidth(15); // Học phí
        $sheet->getColumnDimension('M')->setWidth(15); // Sinh hoạt phí
        $sheet->getColumnDimension('N')->setWidth(15); // CCCD
        $sheet->getColumnDimension('O')->setWidth(25); // Hộ khẩu
        $sheet->getColumnDimension('P')->setWidth(12); // SĐT

        $currentRow = 1;

        foreach ($data as $provinceName => $students) {
            // Header Quốc Hiệu
            $sheet->mergeCells("A{$currentRow}:E{$currentRow}")->setCellValue("A{$currentRow}", 'BỘ GIÁO DỤC VÀ ĐÀO TẠO');
            $sheet->mergeCells("F{$currentRow}:P{$currentRow}")->setCellValue("F{$currentRow}", 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:E{$currentRow}")->setCellValue("A{$currentRow}", 'TRƯỜNG ĐẠI HỌC TÂY NGUYÊN');
            $sheet->mergeCells("F{$currentRow}:P{$currentRow}")->setCellValue("F{$currentRow}", 'Độc lập - Tự do - Hạnh phúc');
            $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("F{$currentRow}:P{$currentRow}")->getFont()->setBold(true)->setUnderline(true);
            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow += 2;

            // Tiêu đề
            $sheet->mergeCells("A{$currentRow}:P{$currentRow}")->setCellValue("A{$currentRow}", "KẾT QUẢ TỐT NGHIỆP CỦA SINH VIÊN ĐƯỢC CẤP HỖ TRỢ TIỀN ĐÓNG HỌC PHÍ, CHI PHÍ SINH HOẠT");
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:P{$currentRow}")->setCellValue("A{$currentRow}", "ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP, CÓ HỘ KHẨU THƯỜNG TRÚ TẠI TỈNH " . mb_strtoupper($provinceName));
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow += 2;

            // Header Bảng (2 dòng)
            $startRow = $currentRow;
            
            // Dòng 1 header
            $sheet->mergeCells("A{$currentRow}:A" . ($currentRow+1))->setCellValue("A{$currentRow}", "STT");
            $sheet->mergeCells("B{$currentRow}:B" . ($currentRow+1))->setCellValue("B{$currentRow}", "Họ và tên");
            $sheet->mergeCells("C{$currentRow}:C" . ($currentRow+1))->setCellValue("C{$currentRow}", "Ngày sinh");
            $sheet->mergeCells("D{$currentRow}:D" . ($currentRow+1))->setCellValue("D{$currentRow}", "Mã số sinh viên");
            $sheet->mergeCells("E{$currentRow}:E" . ($currentRow+1))->setCellValue("E{$currentRow}", "Chuyên ngành");
            $sheet->mergeCells("F{$currentRow}:F" . ($currentRow+1))->setCellValue("F{$currentRow}", "Điểm TB tích lũy");
            $sheet->mergeCells("G{$currentRow}:G" . ($currentRow+1))->setCellValue("G{$currentRow}", "Xếp hạng TN");
            $sheet->mergeCells("H{$currentRow}:H" . ($currentRow+1))->setCellValue("H{$currentRow}", "Điểm RL");
            $sheet->mergeCells("I{$currentRow}:I" . ($currentRow+1))->setCellValue("I{$currentRow}", "Xếp loại RL");
            $sheet->mergeCells("J{$currentRow}:J" . ($currentRow+1))->setCellValue("J{$currentRow}", "Thời gian đào tạo");
            $sheet->mergeCells("K{$currentRow}:K" . ($currentRow+1))->setCellValue("K{$currentRow}", "Quyết định số");
            
            // Gộp cột kinh phí
            $sheet->mergeCells("L{$currentRow}:M{$currentRow}")->setCellValue("L{$currentRow}", "Tổng kinh phí đã thụ hưởng");
            
            $sheet->mergeCells("N{$currentRow}:N" . ($currentRow+1))->setCellValue("N{$currentRow}", "Số CCCD");
            $sheet->mergeCells("O{$currentRow}:O" . ($currentRow+1))->setCellValue("O{$currentRow}", "Hộ khẩu TT");
            $sheet->mergeCells("P{$currentRow}:P" . ($currentRow+1))->setCellValue("P{$currentRow}", "Số điện thoại");
            
            $currentRow++;
            // Dòng 2 header (cho phần kinh phí)
            $sheet->setCellValue("L{$currentRow}", "Học phí");
            $sheet->setCellValue("M{$currentRow}", "Sinh hoạt phí");

            $sheet->getStyle("A{$startRow}:P{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$startRow}:P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle("A{$startRow}:P{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');
            $sheet->getStyle("A{$startRow}:P{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $currentRow++;

            // Dữ liệu
            $stt = 1;
            foreach ($students as $student) {
                $grad = $student->graduation; // Lấy thông tin tốt nghiệp

                $sheet->setCellValue("A{$currentRow}", $stt++);
                $sheet->setCellValue("B{$currentRow}", $student->full_name);
                $sheet->setCellValue("C{$currentRow}", \Carbon\Carbon::parse($student->dob)->format('d/m/Y'));
                $sheet->setCellValueExplicit("D{$currentRow}", $student->student_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("E{$currentRow}", $student->class->major->major_name ?? '');
                
                // Thông tin tốt nghiệp
                $sheet->setCellValue("F{$currentRow}", $grad ? $grad->gpa_final : '');
                $sheet->setCellValue("G{$currentRow}", $grad ? $grad->graduation_rank : '');
                $sheet->setCellValue("H{$currentRow}", $grad ? $grad->conduct_score : '');
                $sheet->setCellValue("I{$currentRow}", $grad ? $grad->conduct_rank : '');
                $sheet->setCellValue("J{$currentRow}", $grad ? $grad->training_time : '');
                $sheet->setCellValue("K{$currentRow}", $grad ? $grad->decision_number : '');

                // Kinh phí (Format số tiền)
                $sheet->setCellValue("L{$currentRow}", $student->total_tuition);
                $sheet->getStyle("L{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->setCellValue("M{$currentRow}", $student->total_living);
                $sheet->getStyle("M{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValueExplicit("N{$currentRow}", $student->citizen_id_card, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("O{$currentRow}", $student->address_detail . ' - ' . ($student->ward->name ?? ''));
                $sheet->setCellValueExplicit("P{$currentRow}", $student->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $currentRow++;
            }

            // Kẻ khung dữ liệu
            $sheet->getStyle("A" . ($startRow + 2) . ":P" . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Footer
            $currentRow++;
            $sheet->setCellValue("B{$currentRow}", "Danh sách gồm: " . $students->count() . " sinh viên.");
            $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true)->setItalic(true);
            
            $currentRow += 2;
            $sheet->mergeCells("L{$currentRow}:P{$currentRow}")->setCellValue("L{$currentRow}", "HIỆU TRƯỞNG");
            $sheet->getStyle("L{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $currentRow += 4;
            $sheet->mergeCells("L{$currentRow}:P{$currentRow}")->setCellValue("L{$currentRow}", "(Đã ký)");
            $sheet->getStyle("L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setBold(true);

            // Ngắt trang
            $sheet->setBreak("A{$currentRow}", Worksheet::BREAK_ROW);
            $currentRow += 2;
        }

        $fileName = 'TK03_Tot_nghiep_K' . $year . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Logic lấy dữ liệu
     */
    private function getReportData(Request $request)
    {
        $year = $request->input('course_year');
        $provinceCode = $request->input('province_code');
        $decisionNumber = $request->input('decision_number');

        $query = Student::query()
            ->with(['class.major', 'province', 'ward', 'graduation'])
            // Chỉ lấy sinh viên ĐÃ TỐT NGHIỆP (có bản ghi trong 116_graduations)
            ->whereHas('graduation')
            ->whereHas('class', function($q) use ($year) {
                if ($year) {
                    $q->where('course_year', $year);
                }
            });

        // Lọc theo Tỉnh
        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        // Lọc theo Số quyết định (để tách đợt)
        if ($decisionNumber) {
            $query->whereHas('graduation', function($q) use ($decisionNumber) {
                $q->where('decision_number', $decisionNumber);
            });
        }

        $students = $query->orderBy('province_code')->orderBy('full_name')->get();

        // Tính toán tổng kinh phí cho từng sinh viên
        // Cách tối ưu: Load trước hoặc dùng subquery, nhưng loop ở đây đơn giản hơn cho logic phức tạp
        foreach ($students as $student) {
            // 1. Học phí
            $student->total_tuition = DB::table('116_tuition_grants')
                ->where('student_code', $student->student_code)
                ->sum('grant_amount');

            // 2. Sinh hoạt phí (tháng)
            $monthly = DB::table('116_monthly_allowances')
                ->where('student_code', $student->student_code)
                ->where('status', 'Đã chi trả')
                ->sum('amount');
            
            // 3. Sinh hoạt phí (kỳ - cũ)
            $semester = DB::table('116_semester_allowances')
                ->where('student_code', $student->student_code)
                ->where('status', 'Đã chi trả')
                ->sum('amount');

            $student->total_living = $monthly + $semester;
        }

        return $students->groupBy(function($student) {
            return $student->province ? $student->province->name : 'Chưa cập nhật Tỉnh';
        })->sortKeys();
    }
}