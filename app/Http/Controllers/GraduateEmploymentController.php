<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\GraduateEmployment;
use App\Models\Province; // Thêm model Province
use App\Models\Ward; // Thêm model Ward
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use App\Models\Setting; // <-- THÊM DÒNG NÀY VÀO ĐÂY

class GraduateEmploymentController extends Controller
{
    /**
     * Hiển thị trang yêu cầu nhập MSSV và CCCD.
     */
    public function showVerificationForm()
    {
        // === BẠN THÊM ĐOẠN CODE KIỂM TRA VÀO ĐÂY ===
        // Mặc định là 'true' (bật) nếu không tìm thấy cấu hình
        if (!Setting::getValue('enable_graduate_form', true)) { 
             
            // Nếu cấu hình là 'OFF', quay lại trang trước với thông báo lỗi
            return redirect()->back()->withErrors([
                'message' => 'Chức năng khai báo việc làm hiện đang tạm đóng. Vui lòng quay lại sau.'
            ]);
       }
       // =============================================
        
        
        return view('graduate.employment.verify');
    }

    /**
     * Kiểm tra MSSV, CCCD và hiển thị form khai báo nếu hợp lệ.
     */
    public function verifyAndShowEmploymentForm(Request $request)
    {
        $request->validate([
            'student_code' => ['required', 'string'],
            'citizen_id_card' => ['required', 'string'],
        ]);

        $student = Student::where('student_code', $request->student_code)
                          ->where('citizen_id_card', $request->citizen_id_card)
                          ->where('status', 'Tốt nghiệp')
                          ->first();

        if (!$student) {
             throw ValidationException::withMessages([
                'student_code' => __('Thông tin Mã sinh viên hoặc CCCD không chính xác, hoặc sinh viên chưa tốt nghiệp.'),
            ]);
        }

        Session::put('verified_student_code', $student->student_code);
        $employmentInfo = GraduateEmployment::where('student_code', $student->student_code)->first();
        
        // Lấy danh sách Tỉnh
        $provinces = Province::orderBy('name')->get();
        
        // Lấy danh sách Xã/Phường ban đầu (nếu đã có thông tin)
        $wards = collect(); // Mặc định là rỗng
        if ($employmentInfo && $employmentInfo->contact_province_code) {
            $wards = Ward::where('province_code', $employmentInfo->contact_province_code)->orderBy('name')->get();
        } elseif($student->province_code) {
             // Nếu SV chưa khai báo, thử lấy ds xã/phường theo tỉnh của SV
             $wards = Ward::where('province_code', $student->province_code)->orderBy('name')->get();
        }

        if (!$employmentInfo) {
            $employmentInfo = new GraduateEmployment([
                'contact_email' => $student->email,
                'contact_phone' => $student->phone,
                // Lấy địa chỉ mặc định từ hồ sơ SV
                'contact_province_code' => $student->province_code,
                'contact_ward_code' => $student->ward_code,
                'contact_address_detail' => $student->address_detail ?: $student->old_address_detail,
            ]);
        }

        return view('graduate.employment.form', compact('student', 'employmentInfo', 'provinces', 'wards'));
    }
    
    /**
     * Hàm lấy Xã/Phường cho AJAX
     */
    public function getWardsByProvince($province_code)
    {
        $wards = Ward::where('province_code', $province_code)->orderBy('name')->get(['code', 'name']);
        return response()->json($wards);
    }

    /**
     * Lưu hoặc cập nhật thông tin khai báo việc làm.
     */
    public function storeOrUpdate(Request $request)
    {
        $studentCode = Session::get('verified_student_code');

        if (!$studentCode) {
            return redirect()->route('graduate.employment.verify')->withErrors(['message' => 'Phiên làm việc hết hạn hoặc không hợp lệ. Vui lòng xác thực lại.']);
        }

        // Cập nhật validation rules
        $validatedData = $request->validate([
            'employment_status' => ['required', 'string', 'in:Đã có việc làm,Chưa có việc làm,Đang học nâng cao,Khác'],
            'job_title' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'max:255'],
            'company_name' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_address' => ['nullable', 'string'],
            'employment_type' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'in:Đúng ngành đào tạo,Trái ngành đào tạo'],
            'start_date' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'date'],
            'contract_type' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'in:Hợp đồng xác định thời hạn,Hợp đồng không xác định thời hạn,Khác'],
            'is_teaching_related' => ['nullable', 'boolean'],
            
            // Rules cho trường mới
            'teaching_province_code' => ['nullable', 'required_if:is_teaching_related,true', 'string', 'exists:116_provinces,code'],
            'contact_province_code' => ['required', 'string', 'exists:116_provinces,code'],
            'contact_ward_code' => ['required', 'string', 'exists:116_wards,code'],
            'contact_address_detail' => ['required', 'string', 'max:255'],

            'contact_email' => ['required', 'email', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:15'],
            'notes' => ['nullable', 'string'],
        ]);

        $validatedData['student_code'] = $studentCode;
        $validatedData['declaration_date'] = Carbon::now();
        $validatedData['is_teaching_related'] = $request->has('is_teaching_related');

        GraduateEmployment::updateOrCreate(
            ['student_code' => $studentCode],
            $validatedData 
        );

        Session::forget('verified_student_code');

        return redirect()->route('graduate.employment.verify')->with('status', 'Khai báo/Cập nhật thông tin việc làm thành công!');
    }
}