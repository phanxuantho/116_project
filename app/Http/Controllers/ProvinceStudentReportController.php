<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use App\Models\ClassModel;
use App\Exports\ProvinceStudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProvinceStudentReportController extends Controller
{
    /**
     * 1. Hiển thị Form bộ lọc
     */
    public function index()
    {
        // Lấy danh sách các năm khóa học từ bảng Lớp (để lọc theo Khóa)
        $courseYears = ClassModel::distinct()->orderBy('course_year', 'desc')->pluck('course_year');
        
        // Lấy danh sách tỉnh
        $provinces = Province::orderBy('name')->get();

        return view('reports.province_students.form', compact('courseYears', 'provinces'));
    }

    /**
     * 2. Xử lý In Báo cáo (Hiển thị HTML View để in)
     */
    public function print(Request $request)
    {
        $data = $this->getReportData($request);
        $year = $request->course_year;

        // Trả về view in ấn (không có layout website, chỉ có nội dung in)
        return view('reports.province_students.print', compact('data', 'year'));
    }

    /**
     * 3. Xuất ra file Excel
     */
    public function export(Request $request)
    {
        $data = $this->getReportData($request);
        $year = $request->course_year;

        // Tên file xuất ra
        $fileName = 'TK01_Danh_sach_SV_Tinh_K' . $year . '.xlsx';

        return Excel::download(new ProvinceStudentsExport($data, $year), $fileName);
    }

    /**
     * Hàm phụ trợ: Lấy và gom nhóm dữ liệu
     */
    private function getReportData(Request $request)
    {
        $year = $request->input('course_year');
        $provinceCode = $request->input('province_code');

        // Query cơ bản
        $query = Student::query()
            ->with(['class', 'province', 'ward', 'class.major', 'class.faculty']) // Eager load để tránh N+1 query
            // Lọc theo Khóa học (năm tuyển sinh)
            ->whereHas('class', function($q) use ($year) {
                if ($year) {
                    $q->where('course_year', $year);
                }
            })
            // Lọc theo trạng thái nhận 116 (Nếu cần thiết)
            // ->where('funding_status', 'Đang nhận') 
            ->orderBy('province_code') // Sắp xếp để gom nhóm đẹp
            ->orderBy('class_id')
            ->orderBy('full_name');

        // Nếu người dùng chọn 1 tỉnh cụ thể thì lọc
        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        $students = $query->get();

        // Gom nhóm sinh viên theo Tên Tỉnh
        // Kết quả trả về là Collection: ['Tên Tỉnh A' => [Danh sách SV], 'Tên Tỉnh B' => [...]]
        return $students->groupBy(function($student) {
            return $student->province ? $student->province->name : 'Chưa cập nhật Tỉnh';
        });
    }
}