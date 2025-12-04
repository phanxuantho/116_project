<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

// Import thư viện PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Thêm import này để dùng hằng số BREAK_ROW

class ProvinceStudentReportController extends Controller
{
    /**
     * 1. Hiển thị Form bộ lọc
     */
    public function index()
    {
        $courseYears = ClassModel::distinct()->orderBy('course_year', 'desc')->pluck('course_year');
        $provinces = Province::orderBy('name')->get();

        return view('reports.province_students.form', compact('courseYears', 'provinces'));
    }

    /**
     * 2. Xử lý In Báo cáo (HTML View)
     */
    public function print(Request $request)
    {
        $data = $this->getReportData($request);
        $year = $request->course_year;

        return view('reports.province_students.print', compact('data', 'year'));
    }

    /**
     * 3. Xuất ra file Excel (Sử dụng PhpSpreadsheet)
     */
    public function export(Request $request)
    {
        // 1. Validate và lấy dữ liệu
        $request->validate(['course_year' => 'required']);
        $data = $this->getReportData($request);
        $year = $request->course_year;

        // 2. Khởi tạo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Cấu hình Font mặc định: Times New Roman, cỡ 11
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(11);

        // Thiết lập chiều rộng cột (tương đối)
        $sheet->getColumnDimension('A')->setWidth(5);  // STT
        $sheet->getColumnDimension('B')->setWidth(25); // Họ tên
        $sheet->getColumnDimension('C')->setWidth(15); // MSSV
        $sheet->getColumnDimension('D')->setWidth(25); // Lớp
        $sheet->getColumnDimension('E')->setWidth(20); // Khoa
        $sheet->getColumnDimension('F')->setWidth(30); // Hộ khẩu
        $sheet->getColumnDimension('G')->setWidth(15); // Tỉnh
        $sheet->getColumnDimension('H')->setWidth(15); // CCCD
        $sheet->getColumnDimension('I')->setWidth(15); // SĐT

        $currentRow = 1;

        // 3. Duyệt qua từng Tỉnh để vẽ từng trang báo cáo
        foreach ($data as $provinceName => $students) {
            
            // --- HEADER QUỐC HIỆU & TÊN TRƯỜNG ---
            $startHeaderRow = $currentRow;
            
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", 'BỘ GIÁO DỤC VÀ ĐÀO TẠO');
            $sheet->mergeCells("E{$currentRow}:I{$currentRow}")->setCellValue("E{$currentRow}", 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
            // Sửa lỗi: Tách biệt setFont và setAlignment
            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:D{$currentRow}")->setCellValue("A{$currentRow}", 'TRƯỜNG ĐẠI HỌC TÂY NGUYÊN');
            $sheet->mergeCells("E{$currentRow}:I{$currentRow}")->setCellValue("E{$currentRow}", 'Độc lập - Tự do - Hạnh phúc');
            
            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("E{$currentRow}:I{$currentRow}")->getFont()->setBold(true)->setUnderline(true);
            $sheet->getStyle("E{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $currentRow += 2; // Cách dòng

            // --- TIÊU ĐỀ BÁO CÁO ---
            $sheet->mergeCells("A{$currentRow}:I{$currentRow}")->setCellValue("A{$currentRow}", "DANH SÁCH SINH VIÊN KHOA TUYỂN SINH {$year} ĐƯỢC CẤP HỖ TRỢ");
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:I{$currentRow}")->setCellValue("A{$currentRow}", "TIỀN ĐÓNG HỌC PHÍ, CHI PHÍ SINH HOẠT ĐỐI VỚI SINH VIÊN SƯ PHẠM");
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;

            $sheet->mergeCells("A{$currentRow}:I{$currentRow}")->setCellValue("A{$currentRow}", "THEO NGHỊ ĐỊNH 116/2020/NĐ-CP, CÓ HỘ KHẨU THƯỜNG TRÚ TẠI TỈNH " . mb_strtoupper($provinceName));
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $currentRow += 2; // Cách dòng

            // --- HEADER BẢNG DỮ LIỆU ---
            $tableStartRow = $currentRow;
            $headers = ['STT', 'HỌ VÀ TÊN', 'MSSV', 'LỚP/NĂM TUYỂN SINH', 'KHOA', 'Hộ khẩu TT (Xã, Huyện)', 'Tỉnh', 'CCCD', 'Điện thoại'];
            
            $colIndex = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue("{$colIndex}{$currentRow}", $header);
                $colIndex++;
            }

            // Style Header Bảng
            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'E0E0E0'] // Màu xám nhẹ
                ]
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(30);
            $currentRow++;

            // --- DỮ LIỆU SINH VIÊN ---
            $stt = 1;
            foreach ($students as $student) {
                $sheet->setCellValue("A{$currentRow}", $stt++);
                $sheet->setCellValue("B{$currentRow}", $student->full_name);
                // MSSV để dạng chuỗi tránh mất số 0
                $sheet->setCellValueExplicit("C{$currentRow}", $student->student_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
                // Lớp và Năm học (xuống dòng trong ô)
                $classInfo = ($student->class->class_name ?? '') . "\n(" . ($student->class->course_year ?? '') . ")";
                $sheet->setCellValue("D{$currentRow}", $classInfo);
                
                $sheet->setCellValue("E{$currentRow}", $student->class->faculty->faculty_name ?? '');
                
                // Địa chỉ
                $address = ($student->address_detail ?? '') . ' - ' . ($student->ward->name ?? '');
                $sheet->setCellValue("F{$currentRow}", $address);
                
                $sheet->setCellValue("G{$currentRow}", $provinceName);
                $sheet->setCellValueExplicit("H{$currentRow}", $student->citizen_id_card, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("I{$currentRow}", $student->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $currentRow++;
            }

            // Kẻ khung bảng
            $tableEndRow = $currentRow - 1;
            $sheet->getStyle("A{$tableStartRow}:I{$tableEndRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true // Tự động xuống dòng nếu dài
                ]
            ]);
            // Căn giữa STT, MSSV, Năm, Tỉnh
            $sheet->getStyle("A{$tableStartRow}:A{$tableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$tableStartRow}:C{$tableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$tableStartRow}:I{$tableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


            // --- FOOTER TỔNG KẾT & CHỮ KÝ ---
            $currentRow++;
            $sheet->setCellValue("B{$currentRow}", "Danh sách gồm: " . $students->count() . " sinh viên.");
            // Sửa lỗi: setItalic thuộc về Font
            $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true)->setItalic(true);
            
            $currentRow += 2;
            
            // Ngày tháng và Chữ ký
            $sheet->mergeCells("E{$currentRow}:I{$currentRow}")->setCellValue("E{$currentRow}", "Đắk Lắk, ngày ...... tháng ...... năm ......");
            // Sửa lỗi: Tách setAlignment và setFont
            $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}")->getFont()->setItalic(true);
            $currentRow++;

            $sheet->mergeCells("E{$currentRow}:I{$currentRow}")->setCellValue("E{$currentRow}", "HIỆU TRƯỞNG");
            // Sửa lỗi: Tách setAlignment và setFont
            $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}")->getFont()->setBold(true);
            
            $currentRow += 4; // Khoảng trống ký tên
            
            $sheet->mergeCells("E{$currentRow}:I{$currentRow}")->setCellValue("E{$currentRow}", "(Đã ký)");
            // Sửa lỗi: Tách setAlignment và setFont
            $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}")->getFont()->setBold(true);

            // --- NGẮT TRANG (PAGE BREAK) ---
            $sheet->setBreak("A{$currentRow}", Worksheet::BREAK_ROW);
            
            $currentRow += 2; // Cách ra một chút cho tỉnh tiếp theo
        }

        // 4. Xuất File
        $fileName = 'TK01_Danh_sach_SV_Tinh_K' . $year . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Hàm phụ trợ lấy dữ liệu (Giữ nguyên)
     */
    private function getReportData(Request $request)
    {
        $year = $request->input('course_year');
        $provinceCode = $request->input('province_code');

        $query = Student::query()
            ->with(['class', 'province', 'ward', 'class.major', 'class.faculty'])
            ->whereHas('class', function($q) use ($year) {
                if ($year) {
                    $q->where('course_year', $year);
                }
            })
            //->where('funding_status', 'Đang nhận') // Bỏ comment nếu cần
            ->orderBy('class_id')
            ->orderBy('full_name');

        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        $students = $query->get();

        return $students->groupBy(function($student) {
            return $student->province ? $student->province->name : 'Chưa cập nhật Tỉnh';
        })->sortKeys();
    }
}