<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProvinceStudentResultReportController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        // Lấy danh sách học kỳ (1, 2, 3...) để fill vào dropdown
        $semesters = [1, 2, 3]; 
        $provinces = Province::orderBy('name')->get();

        return view('reports.province_results.form', compact('schoolYears', 'semesters', 'provinces'));
    }

    public function print(Request $request)
    {
        // Validate dữ liệu
        $this->validateRequest($request);

        $data = $this->getReportData($request);
        $meta = $this->getReportMeta($request);

        return view('reports.province_results.print', compact('data', 'meta'));
    }

    public function export(Request $request)
    {
        $this->validateRequest($request);
        
        $data = $this->getReportData($request);
        $meta = $this->getReportMeta($request);

        // --- XỬ LÝ EXCEL (PhpSpreadsheet) ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(11);

        // Định dạng cột
        $sheet->getColumnDimension('A')->setWidth(5);  // STT
        $sheet->getColumnDimension('B')->setWidth(25); // Họ tên
        $sheet->getColumnDimension('C')->setWidth(15); // MSSV
        $sheet->getColumnDimension('D')->setWidth(25); // Lớp
        $sheet->getColumnDimension('E')->setWidth(10); // KQHT
        $sheet->getColumnDimension('F')->setWidth(10); // KQRL
        $sheet->getColumnDimension('G')->setWidth(25); // Hộ khẩu
        $sheet->getColumnDimension('H')->setWidth(15); // Tỉnh
        $sheet->getColumnDimension('I')->setWidth(15); // CCCD
        $sheet->getColumnDimension('J')->setWidth(15); // SĐT
        $sheet->getColumnDimension('K')->setWidth(15); // Ghi chú

        $currentRow = 1;

        foreach ($data as $provinceName => $students) {
            // ... (Phần Header giống TK01, chỉ đổi Tiêu đề) ...
            $startHeaderRow = $currentRow;
            
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", 'BỘ GIÁO DỤC VÀ ĐÀO TẠO');
            $sheet->mergeCells("E{$currentRow}:K{$currentRow}")->setCellValue("E{$currentRow}", 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", 'TRƯỜNG ĐẠI HỌC TÂY NGUYÊN');
            $sheet->mergeCells("E{$currentRow}:K{$currentRow}")->setCellValue("E{$currentRow}", 'Độc lập - Tự do - Hạnh phúc');
            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("E{$currentRow}:K{$currentRow}")->getFont()->setBold(true)->setUnderline(true);
            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow += 2;

            // Tiêu đề TK02
            $title = "KẾT QUẢ HỌC TẬP VÀ RÈN LUYỆN HỌC KỲ {$meta['semester']} NĂM HỌC {$meta['year_name']}";
            $sheet->mergeCells("A{$currentRow}:K{$currentRow}")->setCellValue("A{$currentRow}", $title);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $subTitle = "CỦA SINH VIÊN ĐƯỢC CẤP HỖ TRỢ TIỀN ĐÓNG HỌC PHÍ, CHI PHÍ SINH HOẠT ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP, CÓ HỘ KHẨU THƯỜNG TRÚ TẠI TỈNH " . mb_strtoupper($provinceName);
            $sheet->mergeCells("A{$currentRow}:K{$currentRow}")->setCellValue("A{$currentRow}", $subTitle);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
            $sheet->getRowDimension($currentRow)->setRowHeight(40); // Tăng chiều cao vì tiêu đề dài
            $currentRow += 2;

            // Header Bảng
            $tableStartRow = $currentRow;
            $headers = ['STT', 'Họ và tên', 'MSSV', 'Lớp/Năm tuyển sinh', 'Kết quả học tập', 'Kết quả rèn luyện', 'Hộ khẩu TT', 'Tỉnh/Thành phố', 'Số CCCD', 'Điện thoại', 'Ghi chú'];
            $col = 'A';
            foreach($headers as $header) {
                $sheet->setCellValue("{$col}{$currentRow}", $header);
                $col++;
            }
            
            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');
            $currentRow++;

            // Dữ liệu
            $stt = 1;
            foreach ($students as $student) {
                // Lấy điểm từ relation đã eager load
                // academicResults là collection, ta lấy bản ghi đầu tiên khớp (vì đã filter trong query chính)
                $result = $student->academicResults->first(); 

                $sheet->setCellValue("A{$currentRow}", $stt++);
                $sheet->setCellValue("B{$currentRow}", $student->full_name);
                $sheet->setCellValueExplicit("C{$currentRow}", $student->student_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
                $classInfo = ($student->class->class_name ?? '') . "\n(" . ($student->class->course_year ?? '') . ")";
                $sheet->setCellValue("D{$currentRow}", $classInfo);
                
                // Điểm
                $sheet->setCellValue("E{$currentRow}", $result ? $result->academic_score : '');
                $sheet->setCellValue("F{$currentRow}", $result ? $result->conduct_score : '');

                $address = ($student->address_detail ?? '') . ' - ' . ($student->ward->name ?? '');
                $sheet->setCellValue("F{$currentRow}", $address);
                $sheet->setCellValue("H{$currentRow}", $provinceName);
                $sheet->setCellValueExplicit("I{$currentRow}", $student->citizen_id_card, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("J{$currentRow}", $student->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("K{$currentRow}", ''); // Ghi chú

                $currentRow++;
            }

            // Kẻ khung
            $sheet->getStyle("A{$tableStartRow}:K" . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Footer
            $currentRow++;
            $sheet->setCellValue("B{$currentRow}", "Danh sách gồm: " . $students->count() . " sinh viên.");
            $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true)->setItalic(true);
            
            $currentRow += 2;
            $sheet->mergeCells("G{$currentRow}:K{$currentRow}")->setCellValue("G{$currentRow}", "HIỆU TRƯỞNG");
            $sheet->getStyle("G{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $currentRow += 4;
            $sheet->mergeCells("G{$currentRow}:K{$currentRow}")->setCellValue("G{$currentRow}", "(Đã ký)");
            $sheet->getStyle("G{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Ngắt trang
            $sheet->setBreak("A{$currentRow}", Worksheet::BREAK_ROW);
            $currentRow += 2;
        }

        $fileName = 'TK02_Ket_qua_hoc_tap_' . $meta['year_name'] . '_HK' . $meta['semester'] . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function validateRequest($request)
    {
        $request->validate([
            'school_year_id' => 'required',
            'semester' => 'required',
        ], [
            'school_year_id.required' => 'Vui lòng chọn Năm học.',
            'semester.required' => 'Vui lòng chọn Học kỳ.',
        ]);
    }

    private function getReportMeta($request)
    {
        $schoolYear = SchoolYear::find($request->school_year_id);
        return [
            'year_name' => $schoolYear ? $schoolYear->name : '', // VD: 2024-2025
            'semester' => $request->semester,
        ];
    }

    /**
     * LOGIC LẤY DỮ LIỆU CỐT LÕI
     */
    private function getReportData(Request $request)
    {
        $schoolYearId = $request->school_year_id;
        $semesterNum = $request->semester;
        $provinceCode = $request->province_code;

        // 1. Tìm ID học kỳ trong CSDL dựa trên năm học và số học kỳ
        $semesterDb = Semester::where('school_year_id', $schoolYearId)
                              ->where('semester_number', $semesterNum)
                              ->first();
        
        if (!$semesterDb) {
            return collect(); // Trả về rỗng nếu không thấy kỳ
        }

        // Lấy thông tin năm học để so sánh logic "lớp ra trường"
        // Giả sử bảng SchoolYear có cột 'start_year' (int) hoặc parse từ tên
        // Ở đây giả định ta lấy năm bắt đầu từ bảng school_years để so sánh
        $selectedSchoolYear = SchoolYear::find($schoolYearId);
        // Logic đơn giản: Lớp có 'course_year' <= Năm của kỳ báo cáo
        // Và xử lý logic Status

        $query = Student::query()
            ->with(['class', 'province', 'ward'])
            // Eager load điểm thi CỦA ĐÚNG KỲ ĐÓ
            ->with(['academicResults' => function($q) use ($semesterDb) {
                $q->where('semester_id', $semesterDb->id);
            }])
             //Chỉ lấy SV có điểm trong kỳ đó (tức là có học)
            ->whereHas('academicResults', function($q) use ($semesterDb) {
                $q->where('semester_id', $semesterDb->id);
            });

        // --- LOGIC LỌC THEO YÊU CẦU ---
        // "Chọn năm học thì chỉ xuất những lớp từ năm đó trở về trước"
        // "Nếu lớp đã ra trường thì chỉ xuất những sinh viên có status = Gia hạn"
        
        // Ta cần check từng sinh viên hoặc dùng whereHas class phức tạp.
        // Cách tốt nhất: Lọc Class trước
        $query->whereHas('class', function($q) use ($selectedSchoolYear) {
            // Giả sử 'course_year' là năm bắt đầu khóa học (2021, 2022...)
            // Cần so sánh với năm của School Year được chọn.
            // Ví dụ: Năm học 2024-2025 => Lấy K2024, K2023, K2022...
            // $startYearOfReport = substr($selectedSchoolYear->name, 0, 4); // VD: 2024
            // $q->where('course_year', '<=', $startYearOfReport);
            
            // TUY NHIÊN, yêu cầu về "Lớp đã ra trường" phức tạp hơn.
            // Ta sẽ xử lý logic này bằng cách nhóm điều kiện OR:
            
            $q->where(function($classQuery) {
                // Nhóm 1: Lớp Đang học (Lấy hết)
                $classQuery->where('class_status', 'Đang học')
                           ->orWhere(function($subQ) {
                                // Nhóm 2: Lớp Đã tốt nghiệp nhưng SV phải là Gia hạn
                                // Lưu ý: Điều kiện SV status phải viết ở query ngoài, 
                                // ở đây ta chỉ filter Class thôi thì chưa đủ.
                                // Nên ta sẽ filter Class chung chung trước.
                                $subQ->where('class_status', 'Đã tốt nghiệp');
                           });
            });
        });

        // Áp dụng logic Status Sinh viên
        $query->where(function($q) {
            // Trường hợp 1: Lớp Đang học => Lấy SV Đang học, Bảo lưu... (Thường là Đang học)
            $q->whereHas('class', function($c) {
                $c->where('class_status', 'Đang học');
            })->whereIn('status', ['Đang học', 'Gia hạn']); // Lấy các trạng thái active

            // Trường hợp 2: Lớp Đã tốt nghiệp => CHỈ LẤY SV GIA HẠN
            $q->orWhere(function($sq) {
                $sq->whereHas('class', function($c) {
                    $c->where('class_status', 'Đã tốt nghiệp');
                })->where('status', 'Gia hạn'); // BẮT BUỘC
            });
        });

        // Lọc theo Tỉnh
        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        $students = $query->orderBy('province_code')
                          ->orderBy('class_id')
                          ->orderBy('full_name')
                          ->get();

        return $students->groupBy(function($student) {
            return $student->province ? $student->province->name : 'Chưa cập nhật Tỉnh';
        })->sortKeys();
    }
}