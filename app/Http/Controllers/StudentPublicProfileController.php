<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class StudentPublicProfileController extends Controller
{
    // ... (Hàm showVerificationForm và verifyAndShowProfileForm giữ nguyên) ...
    public function showVerificationForm()
    {
        return view('student_update.verify');
    }

    public function verifyAndShowProfileForm(Request $request)
    {
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

        return view('student_update.edit', compact('student', 'provinces', 'wards'));
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
            'email' => ['nullable', 'string', 'email', 'max:100', Rule::unique('116_students')->ignore($student->student_code, 'student_code')],
            'phone' => ['nullable', 'string', 'max:15'],
            'province_code' => ['nullable', 'string', 'exists:116_provinces,code'],
            'ward_code' => ['nullable', 'string', 'exists:116_wards,code'],
            'address_detail' => ['nullable', 'string', 'max:255'],
        ]);
        // Các trường 'bank_account', 'bank_name', 'bank_branch' đã bị loại bỏ
        // ----------------------------------------------------

        // Cập nhật sinh viên CHỈ VỚI 5 TRƯỜNG TRÊN
        $student->update($validatedData);

        Session::forget('verified_student_code_for_profile');

        return redirect()->route('student.update.verify')->with('status', 'Cập nhật thông tin cá nhân thành công!');
    }
}