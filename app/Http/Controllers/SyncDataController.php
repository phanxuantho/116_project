<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TtnApiService;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\Student;
// 👇 THÊM CÁC MODEL NÀY
use App\Models\AcademicResult;
use App\Models\SchoolYear;
use App\Models\Semester; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class SyncDataController extends Controller
{
    protected $apiService;

    public function __construct(TtnApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        return view('sync_data.index');
    }
    // 1. API: Lấy danh sách tất cả Mã Sinh Viên trong DB
    public function getAllStudentCodes()
    {
        // Chỉ lấy những SV có mã hợp lệ (bỏ qua null/rỗng)
        $codes = Student::whereNotNull('student_code')
                        ->where('student_code', '!=', '')
                        ->pluck('student_code');
        return response()->json(['success' => true, 'codes' => $codes]);
    }

    // 2. API: Kiểm tra & So sánh trạng thái 1 Sinh viên
    public function checkStudentStatus(Request $request)
    {
        $maSV = $request->input('ma_sv');
        
        try {
            // A. Lấy dữ liệu nội bộ (Local DB) - KÈM THEO THÔNG TIN LỚP
            $localStudent = Student::with('class')->where('student_code', $maSV)->first();
            
            if (!$localStudent) {
                return response()->json(['success' => false, 'message' => "Không tìm thấy SV $maSV"]);
            }
            
            // Lấy trạng thái local
            $localStatus = $localStudent->status ?? $localStudent->student_status ?? '(Trống)';
            
            // Lấy Mã Lớp (Thêm mới)
            $classCode = $localStudent->class ? $localStudent->class->class_code : '(Chưa phân lớp)';

            // B. Gọi API Đào tạo (TTN)
            $apiResponse = $this->apiService->getSinhVienInfo($maSV);
            
            $apiRecord = null;
            if (isset($apiResponse['Data']) && is_array($apiResponse['Data']) && count($apiResponse['Data']) > 0) {
                $apiRecord = $apiResponse['Data'][0]; 
            } elseif (is_array($apiResponse) && count($apiResponse) > 0 && isset($apiResponse[0]['TrangThai'])) {
                $apiRecord = $apiResponse[0]; 
            }

            if (!$apiRecord) {
                return response()->json([
                    'success' => true,
                    'is_match' => false,
                    'data' => [
                        'ma_sv' => $maSV,
                        'ho_ten' => $localStudent->fullname,
                        'class_code' => $classCode, // ✅ Thêm lớp
                        'local_status' => $localStatus,
                        'api_status' => 'Không có dữ liệu API',
                    ]
                ]);
            }

            $apiStatus = $apiRecord['TrangThai'] ?? '(Trống)';

            // C. SO SÁNH
            if ($localStatus === 'Tốt nghiệp') $localStatus = 'Đã tốt nghiệp';
            if ($localStatus === 'Bảo lưu') $localStatus = 'Tạm dừng học';
            
            $isMatch = mb_strtolower(trim($localStatus)) === mb_strtolower(trim($apiStatus));

            return response()->json([
                'success' => true,
                'is_match' => $isMatch,
                'data' => [
                    'ma_sv' => $maSV,
                    'ho_ten' => $localStudent->full_name,
                    'class_code' => $classCode, // ✅ Thêm lớp trả về client
                    'local_status' => $localStudent->status ?? $localStudent->student_status,
                    'api_status' => $apiStatus,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 👇 [QUAN TRỌNG] HÀM NÀY ĐANG THIẾU, CẦN THÊM VÀO ĐỂ JS GỌI ĐƯỢC
    public function getAllClassCodes(Request $request)
    {
        $query = ClassModel::query();

        // Lọc: Nếu có gửi 'nam_hoc', chỉ lấy các lớp có khóa (course_year) nhỏ hơn hoặc bằng năm đó
        if ($request->has('nam_hoc') && $request->nam_hoc) {
            // Ví dụ: Chọn năm 2024 -> Lấy course_year 2024, 2023, 2022...
            $query->where('course_year', '<=', $request->nam_hoc);
        }

        $codes = $query->pluck('class_code');
        return response()->json(['success' => true, 'codes' => $codes]);
    }
    
    
    // Hàm lấy dữ liệu từ API trả về JSON cho View xem trước
    public function fetchData(Request $request)
    {
        try {
            $type = $request->input('type');
            $maDV = $request->input('ma_dv');
            $namHoc = $request->input('nam_hoc');
            $hocKy = $request->input('hoc_ky');
            $maLop = $request->input('ma_lop');
            $maSV = $request->input('ma_sv');

            $data = [];

            switch ($type) {
                // --- NHÓM ĐƠN VỊ & CÁN BỘ ---
                case 'units':
                    $data = $this->apiService->getDonVi($maDV);
                    break;
                case 'lecturers': // Mới
                    $data = $this->apiService->getCBVC($maDV);
                    break;
                case 'gio_gdkh': // Mới
                    $data = $this->apiService->getGioGDKH($maDV, $namHoc);
                    break;
                case 'lop_khoa':
                    $data = $this->apiService->getLopKhoa($maDV, $namHoc);
                    break;

                // --- NHÓM LỚP HỌC ---
                case 'sv_lop':
                    $data = $this->apiService->getSinhVienLop($maLop);
                    break;
                case 'kehoach_lop': // Mới
                    $data = $this->apiService->getKeHoachLop($maLop, $namHoc, $hocKy);
                    break;
                case 'bangdiem_lop': // Mới
                    $data = $this->apiService->getBangDiemLop($maLop, $namHoc, $hocKy);
                    break;
                case 'kqht_lop': // Mới
                    $data = $this->apiService->getKQHTLop($maLop, $namHoc, $hocKy);
                    break;

                // --- NHÓM SINH VIÊN ---
                case 'sv_info':
                    $data = $this->apiService->getSinhVienInfo($maSV);
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Loại dữ liệu không hợp lệ']);
            }

            return response()->json(['success' => true, 'message' => 'Lấy dữ liệu thành công', 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function importData(Request $request)
    {
        $type = $request->input('type');
        $data = $request->input('data');

        if (empty($data) || !is_array($data)) {
            return response()->json(['success' => false, 'message' => 'Không có dữ liệu để import']);
        }

        DB::beginTransaction();
        try {
            $count = 0;
            $errors = [];

            switch ($type) {
                // ... (Các case 'units', 'lop_khoa', 'sv_lop' giữ nguyên) ...

                // 👇 THÊM LOGIC XỬ LÝ KẾT QUẢ HỌC TẬP TẠI ĐÂY
                case 'kqht_lop':
                    foreach ($data as $item) {
                        // 1. Tìm hoặc Tạo Năm học (Ví dụ: 2024-2025)
                        // Giả sử bảng school_years có cột 'name'
                        $schoolYear = SchoolYear::firstOrCreate(
                            ['name' => $item['NamHoc']],
                            ['start_year' => substr($item['NamHoc'], 0, 4), 'end_year' => substr($item['NamHoc'], 5, 4)]
                        );

                        // 2. Tìm hoặc Tạo Học kỳ (Ví dụ: Học kỳ 1 của 2024-2025)
                        // Giả sử bảng semesters có cột 'school_year_id' và 'semester_index' (1, 2, 3)
                        $semester = Semester::firstOrCreate(
                            [
                                'school_year_id' => $schoolYear->id,
                                'semester_number' => $item['HocKy']
                            ],
                            [
                                'name' => 'Học kỳ ' . $item['HocKy'] . ' năm ' . $item['NamHoc']
                            ]
                        );

                        // 3. Kiểm tra Sinh viên có tồn tại không
                        $studentExists = Student::where('student_code', $item['MaSV'])->first();
                        
                        if ($studentExists) {
                                // --- 🔥 RÀNG BUỘC: LỚP TỐT NGHIỆP ---
                            // Tìm lớp của sinh viên này
                            $class = ClassModel::find($studentExists->class_id);
                            if ($class && $class->class_status === 'Đã tốt nghiệp') {
                                // Nếu SV không phải 'Đang học', thì BỎ QUA bản ghi này.
                                $svStatus = $studentExists->status;
                                if ($svStatus !== 'Đang học') {
                                    continue; // Next qua vòng lặp, không chèn điểm
                                }
                            }

                            // 4. Update hoặc Insert vào bảng 116_academic_results
                            AcademicResult::updateOrCreate(
                                [
                                    // Điều kiện unique (student_code + semester_id)
                                    'student_code' => $item['MaSV'],
                                    'semester_id'  => $semester->id, 
                                ],
                                [
                                    // Mapping dữ liệu từ JSON sang Database
                                    'academic_score'      => $this->parseScore($item['DiemTB']), // DiemTB
                                    'conduct_score'       => $this->parseScore($item['DiemRL']), // DiemRL
                                    'registered_credits'  => (int)$item['SoTC'],                 // SoTC
                                    
                                    // JSON không có tích lũy, tạm để 0 hoặc bằng số TC đăng ký để tránh lỗi NOT NULL
                                    'accumulated_credits' => (int)$item['TongTCTL'],                 
                                ]
                            );
                            $count++;
                        } else {
                            // Ghi lại lỗi nếu SV chưa có trong hệ thống
                            $errors[] = "SV {$item['MaSV']} chưa tồn tại trong hệ thống, bỏ qua kết quả.";
                        }
                    }
                    break;
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Đã xử lý xong. Thành công: $count bản ghi.",
                'details' => ['errors' => $errors]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Lỗi Import: ' . $e->getMessage()]);
        }
    }

    // Hàm phụ trợ để xử lý điểm số (tránh lỗi null hoặc rỗng)
    private function parseScore($value) {
        if ($value === null || $value === '') return null;
        return (float)$value;
    }

    private function formatDate($dateString) {
        if (!$dateString) return null;
        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }
}