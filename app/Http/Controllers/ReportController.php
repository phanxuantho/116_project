<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;



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
     * Chuẩn bị dữ liệu và hiển thị trang in cho MỘT lớp.
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

 /**
     * THÊM MỚI: Chuẩn bị dữ liệu và hiển thị trang in cho TẤT CẢ các lớp.
     */
    public function printAllMonthlyReview(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'semester' => 'required|string|max:50',
            'school_year' => 'required|string|max:50',
        ]);

        // Lấy tất cả các lớp có sinh viên đang học
        $classes = ClassModel::where('class_status', 'Đang học') // <-- THÊM ĐIỀU KIỆN LỌC NÀY
            ->whereHas('students', function ($query) {
                $query->where('status', 'Đang học');
        })
        ->with(['faculty', 'students' => function ($query) {
            $query->where('status', 'Đang học')->orderBy('full_name');
        }])
        ->orderBy('faculty_id')->orderBy('class_name')->get();

        $data = [
            'classes' => $classes,
            'month' => $request->month,
            'semester' => $request->semester,
            'school_year' => $request->school_year,
        ];

        return view('prints.monthly-student-review-all', $data);
    }

    /**
     * THÊM MỚI: Hiển thị form cho báo cáo tổng quan.
     */
    public function showOverviewForm()
    {
        return view('reports.overview-form');
    }

    /**
     * THÊM MỚI: Xử lý logic và xuất file Excel báo cáo tổng quan.
     */
    public function exportOverview(Request $request)
    {
        $request->validate(['statistic_time' => 'required|string|max:100']);

        $faculties = $this->getReportData();
        $studentStats = $this->getStudentStats();
        $courseTotals = $this->getCourseTotals();
        $facultyTotals = $this->getFacultyTotals($faculties, $studentStats);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // --- Thiết lập Header ---
        $sheet->mergeCells('A1:C1')->setCellValue('A1', 'TRƯỜNG ĐẠI HỌC TÂY NGUYÊN');
        $sheet->mergeCells('D1:I1')->setCellValue('D1', 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
        $sheet->mergeCells('A2:C2')->setCellValue('A2', 'PHÒNG CÔNG TÁC SINH VIÊN');
        $sheet->mergeCells('D2:I2')->setCellValue('D2', 'Độc lập - Tự do - Hạnh phúc');
        $sheet->getStyle('A1:I2')->getFont()->setBold(true);
        $sheet->getStyle('D1:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:I4')->setCellValue('A4', 'BẢNG TỔNG HỢP THEO DÕI TÌNH HÌNH SINH VIÊN NHẬN 116');
        $sheet->getStyle('A4')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->mergeCells('A6:I6')->setCellValue('A6', 'Thời gian thống kê: ' . $request->statistic_time);
        $sheet->getStyle('A6')->getFont()->setItalic(true);

        // --- Tiêu đề bảng ---
        $headers = ['TT', 'Mã lớp', 'Tên lớp', 'Sĩ số', 'Tổng số đăng ký nhận', 'Dừng cấp', 'Tạm dừng cấp', 'Đang cấp', 'Ghi chú'];
        $sheet->fromArray($headers, NULL, 'A8');
        $sheet->getStyle('A8:I8')->getFont()->setBold(true);
        $sheet->getStyle('A8:I8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);

        $currentRow = 9;

        foreach ($faculties as $faculty) {
            $sheet->mergeCells("A{$currentRow}:I{$currentRow}")->setCellValue("A{$currentRow}", 'Khoa: ' . $faculty->faculty_name);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
            $currentRow++;

            $classesByCourse = $faculty->classes->groupBy('course_year');

            foreach ($classesByCourse as $courseYear => $classes) {
                $sheet->mergeCells("B{$currentRow}:I{$currentRow}")->setCellValue("B{$currentRow}", 'Khóa: ' . $courseYear);
                $sheet->getStyle("B{$currentRow}")->getFont()->setItalic(true);
                $currentRow++;
                
                $stt = 1;
                foreach ($classes as $class) {
                    $stats = $studentStats->get($class->id);
                    $sheet->setCellValue('A' . $currentRow, $stt++);
                    $sheet->setCellValue('B' . $currentRow, $class->class_code);
                    $sheet->setCellValue('C' . $currentRow, $class->class_name);
                    $sheet->setCellValue('D' . $currentRow, $class->class_size ?? 0);
                    $sheet->setCellValue('E' . $currentRow, $stats->total_registered ?? 0);
                    $sheet->setCellValue('F' . $currentRow, $stats->total_stopped ?? 0);
                    $sheet->setCellValue('G' . $currentRow, $stats->total_paused ?? 0);
                    $sheet->setCellValue('H' . $currentRow, $stats->total_receiving ?? 0);
                    $currentRow++;
                }
            }

            // Dòng tổng của Khoa
            $totals = $facultyTotals[$faculty->id];
            $sheet->mergeCells("A{$currentRow}:C{$currentRow}")->setCellValue("A{$currentRow}", 'Tổng cộng khoa ' . $faculty->faculty_name . ':');
            $sheet->setCellValue('D' . $currentRow, $totals->enrolled);
            $sheet->setCellValue('E' . $currentRow, $totals->registered);
            $sheet->setCellValue('F' . $currentRow, $totals->stopped);
            $sheet->setCellValue('G' . $currentRow, $totals->paused);
            $sheet->setCellValue('H' . $currentRow, $totals->receiving);
            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_RED);
            $currentRow++;
            $currentRow++; // Thêm dòng trống
        }

        // --- Phần tổng kết cuối báo cáo ---
        $currentRow++; // Dòng trống

        // Dòng tiêu đề tổng cộng toàn trường
       $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFont()->setBold(true);
       $sheet->mergeCells("A{$currentRow}:C{$currentRow}")->setCellValue("A{$currentRow}", 'Tổng cộng theo ngành:');
       $sheet->setCellValue('D' . $currentRow, 'Sĩ số');
       $sheet->setCellValue('E' . $currentRow, 'Tổng số đăng ký nhận');
       $sheet->setCellValue('F' . $currentRow, 'Dừng cấp');
       $sheet->setCellValue('G' . $currentRow, 'Tạm dừng');
       $sheet->setCellValue('H' . $currentRow, 'Đang cấp');
       $currentRow++;

        foreach($courseTotals as $courseData) {
            $sheet->mergeCells("A{$currentRow}:C{$currentRow}")->setCellValue("A{$currentRow}", 'Tổng cộng khóa ' . $courseData->course_year . ':');
            $sheet->setCellValue('D' . $currentRow, $courseData->total_enrolled);
            $sheet->setCellValue('E' . $currentRow, $courseData->total_registered);
            $sheet->setCellValue('F' . $currentRow, $courseData->total_stopped);
            $sheet->setCellValue('G' . $currentRow, $courseData->total_paused);
            $sheet->setCellValue('H' . $currentRow, $courseData->total_receiving);
            $currentRow++;
        }

        // Dòng tổng cộng toàn trường
        $sheet->mergeCells("A{$currentRow}:C{$currentRow}")->setCellValue("A{$currentRow}", 'Tổng cộng toàn trường:');
        $sheet->setCellValue('D' . $currentRow, $courseTotals->sum('total_enrolled'));
        $sheet->setCellValue('E' . $currentRow, $courseTotals->sum('total_registered'));
        $sheet->setCellValue('F' . $currentRow, $courseTotals->sum('total_stopped'));
        $sheet->setCellValue('G' . $currentRow, $courseTotals->sum('total_paused'));
        $sheet->setCellValue('H' . $currentRow, $courseTotals->sum('total_receiving'));
        $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFont()->setBold(true);
        $currentRow += 2; // Thêm khoảng cách

        // Phần ký tên
        $sheet->mergeCells("A{$currentRow}:C{$currentRow}")->setCellValue("A{$currentRow}", 'NGƯỜI LẬP BIỂU');
        $sheet->mergeCells("G{$currentRow}:I{$currentRow}")->setCellValue("G{$currentRow}", 'TRƯỞNG PHÒNG');
        $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Định dạng và Xuất file ---
        $fileName = 'Bao-cao-tong-quan-sinh-vien-116.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }

    /**
     * Chuẩn bị dữ liệu và hiển thị trang in cho báo cáo tổng quan.
     */
    public function printOverview(Request $request)
    {
        $request->validate(['statistic_time' => 'required|string|max:100']);

        $faculties = $this->getReportData();
        $studentStats = $this->getStudentStats();
        $courseTotals = $this->getCourseTotals();
        $facultyTotals = $this->getFacultyTotals($faculties, $studentStats);

        return view('prints.overview-report', [
            'faculties' => $faculties,
            'studentStats' => $studentStats,
            'courseTotals' => $courseTotals,
            'facultyTotals' => $facultyTotals,
            'statistic_time' => $request->statistic_time
        ]);
    }
    
    /**
     * Hàm private để lấy dữ liệu Khoa và Lớp đang học.
     */
    private function getReportData()
    {
        return Faculty::whereHas('classes', function ($query) {
                $query->where('class_status', 'Đang học');
            })
            ->with(['classes' => function ($query) {
                $query->where('class_status', 'Đang học')
                      ->orderBy('course_year', 'desc')->orderBy('class_name');
            }])
            ->orderBy('faculty_name')
            ->get();
    }

    /**
     * Hàm private để lấy dữ liệu thống kê sinh viên.
     */
    private function getStudentStats()
    {
        return Student::select(
            'class_id',
            DB::raw("SUM(CASE WHEN funding_status IS NOT NULL THEN 1 ELSE 0 END) as total_registered"),
            DB::raw("SUM(CASE WHEN funding_status = 'Thôi nhận' THEN 1 ELSE 0 END) as total_stopped"),
            DB::raw("SUM(CASE WHEN funding_status = 'Tạm dừng nhận' THEN 1 ELSE 0 END) as total_paused"),
            DB::raw("SUM(CASE WHEN funding_status = 'Đang nhận' THEN 1 ELSE 0 END) as total_receiving")
        )
        ->groupBy('class_id')
        ->get()
        ->keyBy('class_id');
    }

    /**
     * Hàm private để lấy dữ liệu tổng kết theo khóa.
     */
    private function getCourseTotals()
    {
        $studentData = Student::join('116_classes', '116_students.class_id', '=', '116_classes.id')
            ->where('116_classes.class_status', 'Đang học')
            ->select(
                '116_classes.course_year',
                DB::raw("SUM(CASE WHEN funding_status IS NOT NULL THEN 1 ELSE 0 END) as total_registered"),
                DB::raw('SUM(CASE WHEN funding_status = "Thôi nhận" THEN 1 ELSE 0 END) as total_stopped'),
                DB::raw('SUM(CASE WHEN funding_status = "Tạm dừng nhận" THEN 1 ELSE 0 END) as total_paused'),
                DB::raw('SUM(CASE WHEN funding_status = "Đang nhận" THEN 1 ELSE 0 END) as total_receiving')
            )
            ->groupBy('116_classes.course_year')
            ->get()
            ->keyBy('course_year');

        $classData = ClassModel::where('class_status', 'Đang học')
            ->select(
                'course_year',
                DB::raw('SUM(class_size) as total_enrolled')
            )
            ->groupBy('course_year')
            ->get()
            ->keyBy('course_year');

        $mergedData = $classData->map(function ($item, $key) use ($studentData) {
            $studentItem = $studentData->get($key);
            if ($studentItem) {
                $item->total_registered = $studentItem->total_registered;
                $item->total_stopped = $studentItem->total_stopped;
                $item->total_paused = $studentItem->total_paused;
                $item->total_receiving = $studentItem->total_receiving;
            } else {
                $item->total_registered = 0;
                $item->total_stopped = 0;
                $item->total_paused = 0;
                $item->total_receiving = 0;
            }
            return $item;
        });

        return $mergedData->sortBy('course_year')->values();
    }

    /**
     * Hàm private để tính tổng cho từng khoa.
     */
    private function getFacultyTotals($faculties, $studentStats)
    {
        $facultyTotals = [];
        foreach ($faculties as $faculty) {
            $totals = ['enrolled' => 0, 'registered' => 0, 'stopped' => 0, 'paused' => 0, 'receiving' => 0];
            foreach ($faculty->classes as $class) {
                $stats = $studentStats->get($class->id);
                $totals['enrolled'] += $class->class_size ?? 0;
                $totals['registered'] += $stats->total_registered ?? 0;
                $totals['stopped'] += $stats->total_stopped ?? 0;
                $totals['paused'] += $stats->total_paused ?? 0;
                $totals['receiving'] += $stats->total_receiving ?? 0;
            }
            $facultyTotals[$faculty->id] = (object) $totals;
        }
        return $facultyTotals;
    }
}

