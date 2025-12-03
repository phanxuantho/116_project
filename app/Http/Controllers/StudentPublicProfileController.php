<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 
use App\Models\Setting; // <-- THÊM DÒNG NÀY VÀO ĐÂY

class StudentPublicProfileController extends Controller
{
    // ... (Hàm showVerificationForm và verifyAndShowProfileForm giữ nguyên) ...
    public function showVerificationForm()
    {
 
        // Kiểm tra cấu hình có bật form không
         if (!Setting::getValue('enable_student_update_form', true)) {
            return redirect()->route('student.update.verify')
                   ->withErrors(['message' => 'Chức năng cập nhật thông tin hiện đang tạm đóng.']);
        }
        return view('student_update.verify');
    }

    public function verifyAndShowProfileForm(Request $request)
    {
        // Kiểm tra cấu hình có bật form không
        if (!Setting::getValue('enable_student_update_form', true)) {
            return redirect()->route('student.update.verify')
                   ->withErrors(['message' => 'Chức năng cập nhật thông tin hiện đang tạm đóng.']);
        }
        
        
        
        $request->validate([
            'student_code' => ['required', 'string'],
            'citizen_id_card' => ['required', 'string'],
        ]);

        $student = Student::where('student_code', $request->student_code)
                          ->where('citizen_id_card', $request->citizen_id_card)
                          ->first();

        if (!$student) {
             throw ValidationException::withMessages([
                'student_code' => __('Thông tin Mã sinh viên hoặc CCCD không chính xác.'),
            ]);
        }

        Session::put('verified_student_code_for_profile', $student->student_code);

        $provinces = Province::orderBy('name')->get();
        
        $wards = collect();
        if ($student->province_code) {
            $wards = Ward::where('province_code', $student->province_code)->orderBy('name')->get();
        }

        // === BẮT ĐẦU TÍNH TOÁN KINH PHÍ ===
        $studentCode = $student->student_code;

        // 1. Tổng hỗ trợ Học phí (từ 116_tuition_grants)
        $totalTuitionGrant = DB::table('116_tuition_grants')
                                ->where('student_code', $studentCode)
                                //->whereNotNull('paid_at') // Chỉ tính các khoản đã chi
                                ->sum('grant_amount');

        // 2. Tổng Sinh hoạt phí (từ 116_monthly_allowances)
        $totalMonthlyAllowance = DB::table('116_monthly_allowances')
                                   ->where('student_code', $studentCode)
                                   ->where('status', 'Đã chi trả')
                                   ->sum('amount');
        
        // 3. Tổng Sinh hoạt phí (từ 116_semester_allowances - Dữ liệu cũ trả theo đợt)
        $totalSemesterAllowance = DB::table('116_semester_allowances')
                                    ->where('student_code', $studentCode)
                                    ->where('status', 'Đã chi trả') // Giả định bạn đã thêm cột status
                                    ->sum('amount');
        
        // Cộng dồn 2 khoản sinh hoạt phí
        $totalLivingAllowance = $totalMonthlyAllowance + $totalSemesterAllowance;
        // === KẾT THÚC TÍNH TOÁN KINH PHÍ ===
        
        // Kiểm tra cấu hình có disable input không
        $inputsDisabled = Setting::getValue('disable_student_update_inputs', false); // Mặc định là không disable
        return view('student_update.edit', compact(
            'student', 
            'provinces', 
            'wards',
            'totalTuitionGrant',      // <-- Biến mới
            'totalLivingAllowance',    // <-- Biến mới
            'inputsDisabled' // <-- Truyền biến này sang view
        ));
    }


    /**
     * Lưu thông tin cá nhân đã cập nhật.
     */
    public function updateProfile(Request $request)
    {
        $studentCode = Session::get('verified_student_code_for_profile');

        if (!$studentCode) {
            return redirect()->route('student.update.verify')->withErrors(['message' => 'Phiên làm việc hết hạn. Vui lòng xác thực lại.']);
        }

        $student = Student::find($studentCode);
        if (!$student) {
             return redirect()->route('student.update.verify')->withErrors(['message' => 'Không tìm thấy sinh viên. Vui lòng xác thực lại.']);
        }

        // === BẢO MẬT: CHỈ VALIDATE 5 TRƯỜNG ĐƯỢC PHÉP SỬA ===
        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('116_students')->ignore($student->student_code, 'student_code')],
            'phone' => ['required', 'string', 'max:15'],
            'province_code' => ['required', 'string', 'exists:116_provinces,code'],
            'ward_code' => ['required', 'string', 'exists:116_wards,code'],
            'address_detail' => ['required', 'string', 'max:255'],
        ]);
        // Các trường 'bank_account', 'bank_name', 'bank_branch' đã bị loại bỏ
        // ----------------------------------------------------

        // Cập nhật sinh viên CHỈ VỚI 5 TRƯỜNG TRÊN
        $student->update($validatedData);

        Session::forget('verified_student_code_for_profile');

        return redirect()->route('student.update.verify')->with('status', 'Cập nhật thông tin cá nhân thành công!');
    }
}