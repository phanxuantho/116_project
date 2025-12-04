<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TtnApiService;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\Student;
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

    // Hàm Import dữ liệu vào Database 116
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
                // IMPORT KHOA (FACULTIES)
                case 'units':
                    foreach ($data as $item) {
                        // Giả sử API trả về: MaDV, TenDV
                        Faculty::updateOrCreate(
                            ['code' => $item['MaDV']], 
                            ['name' => $item['TenDV']]
                        );
                        $count++;
                    }
                    break;

                // IMPORT LỚP (CLASSES)
                case 'lop_khoa':
                    foreach ($data as $item) {
                        // Cần tìm ID của Khoa dựa trên mã Khoa trả về từ API
                        $faculty = Faculty::where('code', $item['MaKhoa'] ?? '')->first(); // Check field name API trả về
                        if ($faculty) {
                            ClassModel::updateOrCreate(
                                ['code' => $item['MaLop']],
                                [
                                    'name' => $item['TenLop'],
                                    'faculty_id' => $faculty->id,
                                    // 'course_year' => ... nếu API có trả về
                                ]
                            );
                            $count++;
                        }
                    }
                    break;

                // IMPORT SINH VIÊN (STUDENTS)
                case 'sv_lop':
                    foreach ($data as $item) {
                        // Cần tìm ID của Lớp
                        $class = ClassModel::where('code', $item['MaLop'] ?? '')->first();
                        
                        if ($class) {
                            Student::updateOrCreate(
                                ['student_code' => $item['MaSV']],
                                [
                                    'fullname' => $item['HoTen'] ?? $item['HoVaTen'], // Check key API
                                    'class_id' => $class->id,
                                    'gender' => ($item['GioiTinh'] == 'Nam' ? 'male' : 'female'),
                                    'date_of_birth' => $this->formatDate($item['NgaySinh'] ?? null),
                                    // Map thêm các trường khác như dân tộc, quê quán nếu API có
                                ]
                            );
                            $count++;
                        }
                    }
                    break;
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Đã import thành công $count bản ghi.",
                'details' => ['errors' => $errors]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Lỗi Import: ' . $e->getMessage()]);
        }
    }

    private function formatDate($dateString) {
        if (!$dateString) return null;
        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }
}