<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ReportController extends Controller
{
    /**
     * Hiển thị form cho báo cáo rà soát hàng tháng.
     */
    public function showMonthlyReviewForm()
    {
        $faculties = Faculty::orderBy('faculty_name')->get();
        $classes = ClassModel::orderBy('class_name')->get();

        return view('reports.monthly-review-form', compact('faculties', 'classes'));
    }

    /**
     * Xử lý yêu cầu và xuất ra file Excel bằng PhpSpreadsheet.
     */
    public function exportMonthlyReview(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:116_faculties,id',
            'class_id' => 'required|exists:116_classes,id',
            'month' => 'required|integer|min:1|max:12',
            'semester' => 'required|string|max:50',
            'school_year' => 'required|string|max:50',
        ]);
        
        $faculty = Faculty::findOrFail($request->faculty_id);
        $class = ClassModel::findOrFail($request->class_id);
        $students = Student::where('class_id', $request->class_id)
                             ->where('status', 'Đang học')
                             ->orderBy('full_name')
                             ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // --- Thiết lập Header và Tiêu đề ---
        $sheet->mergeCells('A2:D2')->setCellValue('A2', 'TRƯỜNG ĐẠI HỌC TÂY NGUYÊN');
        $sheet->mergeCells('E2:K2')->setCellValue('E2', 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
        $sheet->mergeCells('A3:D3')->setCellValue('A3', 'KHOA ' . strtoupper($faculty->faculty_name));
        $sheet->mergeCells('E3:K3')->setCellValue('E3', 'Độc lập - Tự do - Hạnh phúc');
        
        $sheet->getStyle('A2:K3')->getFont()->setBold(true);
        $sheet->getStyle('A2:K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A6:K6')->setCellValue('A6', 'DANH SÁCH RÀ SOÁT CẤP HỖ TRỢ HỌC PHÍ, CHI PHÍ SINH HOẠT');
        $sheet->mergeCells('A7:K7')->setCellValue('A7', 'ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP');
        $sheet->mergeCells('A8:K8')->setCellValue('A8', 'THÁNG ' . $request->month . ' HỌC KỲ ' . $request->semester . ' NĂM HỌC ' . $request->school_year);
        
        $sheet->getStyle('A6:A7')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A8')->getFont()->setItalic(true);
        $sheet->getStyle('A6:A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A10:K10')->setCellValue('A10', 'Lớp: ' . $class->class_name);

        // --- Thiết lập Tiêu đề Bảng ---
        $sheet->mergeCells('H11:I11')->setCellValue('H11', 'Học kỳ ' . $request->semester . ' Năm học ' . $request->school_year);
        
        $headers = ['STT', 'Họ và tên', 'MSSV', 'Lớp', 'Khoa', 'Ngân hàng', 'Số tài khoản', 'Dừng nhận', 'Tiếp tục nhận', 'Ký tên', 'Ghi chú'];
        $sheet->fromArray($headers, NULL, 'A12');
        $sheet->getStyle('A11:K12')->getFont()->setBold(true);
        $sheet->getStyle('A11:K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        
        // Gộp các ô cho tiêu đề chính
        $sheet->mergeCells('A11:A12'); $sheet->mergeCells('B11:B12'); $sheet->mergeCells('C11:C12');
        $sheet->mergeCells('D11:D12'); $sheet->mergeCells('E11:E12'); $sheet->mergeCells('F11:F12');
        $sheet->mergeCells('G11:G12'); $sheet->mergeCells('J11:J12'); $sheet->mergeCells('K11:K12');

        // --- Điền dữ liệu Sinh viên ---
        $row = 13;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student->full_name);
            $sheet->setCellValue('C' . $row, $student->student_code);
            $sheet->setCellValue('D' . $row, $student->class->class_name ?? '');
            $sheet->setCellValue('E' . $row, $student->class->faculty->faculty_name ?? '');
            $sheet->setCellValue('F' . $row, $student->bank_name);
            // Định dạng số tài khoản là Text để không bị mất số 0 ở đầu
            $sheet->getCell('G' . $row)->setValueExplicit($student->bank_account, DataType::TYPE_STRING);
            $row++;
        }

        // --- Định dạng Bảng ---
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:K' . $lastRow)->applyFromArray($styleArray);

        // --- Thiết lập Chiều rộng Cột ---
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(20);


        // --- Xuất file ---
        $fileName = 'Danh-sach-ra-soat-' . $class->class_name . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Chuẩn bị dữ liệu và hiển thị trang in.
     */
    public function printMonthlyReview(Request $request)
    {
        $faculty = Faculty::find($request->faculty_id);
        $class = ClassModel::find($request->class_id);
        $students = Student::where('class_id', $request->class_id)
                             ->where('status', 'Đang học')
                             ->orderBy('full_name')
                             ->get();

        $data = [
            'facultyName' => $faculty ? $faculty->faculty_name : 'Tất cả',
            'className' => $class ? $class->class_name : 'Tất cả',
            'students' => $students,
            'month' => $request->month,
            'semester' => $request->semester,
            'school_year' => $request->school_year,
        ];

        return view('prints.monthly-student-review', $data);
    }
}

