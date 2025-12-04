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
                        $studentExists = Student::where('student_code', $item['MaSV'])->exists();
                        
                        if ($studentExists) {
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
                                    'accumulated_credits' => (int)$item['SoTC'],                 
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