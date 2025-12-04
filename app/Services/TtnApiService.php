<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class TtnApiService
{
    protected $baseUrl;
    protected $serverId;
    protected $secretStartEncrypted;
    protected $secretNewEncrypted;
    protected $cacheKey = 'ttn_api_auth_data';

    public function __construct()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->baseUrl = config('services.ttn.base_url');
        $this->serverId = config('services.ttn.server_id');
        $this->secretStartEncrypted = config('services.ttn.secret_start');
        $this->secretNewEncrypted = config('services.ttn.secret_new');
    }

    // === LOGIC MÃ HÓA & HANDSHAKE (Giữ nguyên logic cốt lõi) ===
    private function hamMaHoa($plainText, $secret) {
        $key = hash('sha256', $secret);
        $combined = $plainText . ":" . $key;
        return base64_encode($combined);
    }

    private function hamGiaiMa($cipherText, $secret) {
        try {
            $key = hash('sha256', $secret);
            $decoded = base64_decode($cipherText);
            if ($decoded === false) return null;
            $parts = explode(":", $decoded, 2);
            if (count($parts) !== 2) return null;
            if (hash_equals($parts[1], $key)) return $parts[0];
            return null;
        } catch (Exception $e) { return null; }
    }

    private function hamGiaiMaCoDinh($cipherText) { return $this->hamGiaiMa($cipherText, "Handshake success"); }
    private function hamGiaiMaRoot($cipherText) { return $this->hamGiaiMa($cipherText, "startServer"); }

    private function getAuthData($forceReset = false) {
        if ($forceReset) Cache::forget($this->cacheKey);
        // Cache trong 120 phút để tránh handshake liên tục
        return Cache::remember($this->cacheKey, 120 * 60, function () { return $this->performHandshake(); });
    }

    private function performHandshake() {
        $dateSuffix = date('Ymd');
        $maRoot = $this->hamGiaiMaRoot($this->secretStartEncrypted);
        $maCoDinh = $this->hamGiaiMaCoDinh($this->secretNewEncrypted);
        
        if (!$maRoot || !$maCoDinh) throw new Exception("Lỗi giải mã Secret Key. Kiểm tra .env");

        $keyRoot = $maRoot . $dateSuffix . $maCoDinh;
        $cipherText = $this->hamMaHoa($this->serverId, $keyRoot);

        $response = Http::post($this->baseUrl . '/api/kpigetkey', ['DeviceID' => $this->serverId, 'CipherText' => $cipherText]);
        if (!$response->successful()) throw new Exception("Handshake thất bại: " . $response->status());
        
        $data = $response->json();
        if (!isset($data['MaNgauNhienEncry'])) throw new Exception("API không trả về MaNgauNhienEncry.");

        $decryptionKey = $maCoDinh . $dateSuffix;
        $maNgauNhien = $this->hamGiaiMa($data['MaNgauNhienEncry'], $decryptionKey);
        $deviceCount = (int)$this->hamGiaiMa($data['DeviceCountEncry'], $decryptionKey);

        return ['ma_ngau_nhien' => $maNgauNhien, 'device_count' => $deviceCount, 'ma_co_dinh' => $maCoDinh];
    }

    public function callApi($endpoint, $payloadParams = [], $retry = true) {
        try {
            $auth = $this->getAuthData();
            $maNgauNhien = $auth['ma_ngau_nhien'];
            $deviceCount = $auth['device_count'];
            $maCoDinh = $auth['ma_co_dinh'] ?? $this->hamGiaiMaCoDinh($this->secretNewEncrypted);
            $dateSuffix = date('Ymd');

            $deviceCountMoi = $deviceCount + 1;
            $ID1 = $this->hamMaHoa($this->serverId, $dateSuffix . $maCoDinh);
            $ID2 = $this->hamMaHoa($this->serverId, $maNgauNhien . $dateSuffix . $maCoDinh . $deviceCountMoi);

            $payload = ['DeviceId_Enc1' => $ID1, 'DeviceId_Enc2' => $ID2];
            foreach ($payloadParams as $key => $value) {
                $payload[$key] = $this->hamMaHoa((string)$value, $maNgauNhien);
            }

            $response = Http::post($this->baseUrl . $endpoint, $payload);

            if ($response->status() == 401 && $retry) {
                $this->getAuthData(true);
                return $this->callApi($endpoint, $payloadParams, false);
            }

            if ($response->successful()) {
                $auth['device_count'] = $deviceCountMoi;
                Cache::put($this->cacheKey, $auth, 120 * 60);
                return $response->json();
            }
            throw new Exception("API Error {$endpoint}: " . $response->status());
        } catch (Exception $e) { throw $e; }
    }

    // === WRAPPER FUNCTIONS CHO CONTROLLER ===
    public function getDonVi($maDV) { return $this->callApi('/api/kpiGET_DonVi', ['maDV' => $maDV]); }
    public function getLopKhoa($maKhoa, $namHoc) { return $this->callApi('/api/kpiGetLopKhoa', ['MaKhoa' => $maKhoa, 'NamHoc' => $namHoc]); }
    public function getSinhVienLop($maLop) { return $this->callApi('/api/kpiGetSinhVienLop', ['MaLop' => $maLop]); }
    public function getSinhVienInfo($maSV) { return $this->callApi('/api/kpiGetSinhVien', ['MaSV' => $maSV]); }
    // 1. Lấy danh sách Cán bộ viên chức
    public function getCBVC($maDV) { 
        return $this->callApi('/api/kpiGET_CBVC', ['maDV' => $maDV]); 
    }

    // 2. Lấy Giờ giảng dạy khoa học
    public function getGioGDKH($maDV, $namHoc) { 
        return $this->callApi('/api/kpiGetGioGDKH', ['maDV' => $maDV, 'NamHoc' => $namHoc]); 
    }

    // 3. Lấy Kế hoạch lớp (Thời khóa biểu/Chương trình)
    public function getKeHoachLop($maLop, $namHoc, $hocKy) { 
        return $this->callApi('/api/kpiGetKeHoachLop', ['MaLop' => $maLop, 'NamHoc' => $namHoc, 'HocKy' => $hocKy]); 
    }

    // 4. Lấy Bảng điểm chi tiết của lớp
    public function getBangDiemLop($maLop, $namHoc, $hocKy) { 
        return $this->callApi('/api/kpiGetBangDiemLop', ['MaLop' => $maLop, 'NamHoc' => $namHoc, 'HocKy' => $hocKy]); 
    }

    // 5. Lấy Kết quả học tập tổng hợp
    public function getKQHTLop($maLop, $namHoc, $hocKy) { 
        return $this->callApi('/api/kpiGetKQHTLop', ['MaLop' => $maLop, 'NamHoc' => $namHoc, 'HocKy' => $hocKy]); 
    }
}