<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\GraduateEmployment;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session; // Thêm Session facade

class GraduateEmploymentController extends Controller
{
    /**
     * Hiển thị trang yêu cầu nhập MSSV và CCCD.
     */
    public function showVerificationForm()
    {
        // Sử dụng GuestLayout hoặc tạo layout riêng đơn giản
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

        // Tìm sinh viên đã tốt nghiệp dựa trên student_code và citizen_id_card
        $student = Student::where('student_code', $request->student_code)
                          ->where('citizen_id_card', $request->citizen_id_card)
                          ->where('status', 'Tốt nghiệp') // Chỉ cho SV đã tốt nghiệp
                          ->first();

        // Nếu không tìm thấy hoặc thông tin sai
        if (!$student) {
             // Quay lại trang verify với lỗi
             throw ValidationException::withMessages([
                'student_code' => __('Thông tin Mã sinh viên hoặc CCCD không chính xác, hoặc sinh viên chưa tốt nghiệp.'),
            ]);
        }

        // Lưu mã sinh viên đã xác thực vào session để dùng ở form khai báo
        Session::put('verified_student_code', $student->student_code);

        // Lấy thông tin khai báo việc làm đã có (nếu có)
        $employmentInfo = GraduateEmployment::where('student_code', $student->student_code)->first();

        // Nếu chưa có, chuẩn bị dữ liệu mặc định từ thông tin SV
        if (!$employmentInfo) {
            $employmentInfo = new GraduateEmployment([
                'contact_email' => $student->email,
                'contact_phone' => $student->phone,
                'contact_address' => $student->address_detail ?: $student->old_address_detail,
            ]);
        }

        // Chuyển đến view hiển thị form khai báo
        return view('graduate.employment.form', compact('student', 'employmentInfo'));
    }

    /**
     * Lưu hoặc cập nhật thông tin khai báo việc làm.
     */
    public function storeOrUpdate(Request $request)
    {
        // Lấy mã sinh viên đã xác thực từ session
        $studentCode = Session::get('verified_student_code');

        // Kiểm tra xem session có tồn tại không (tránh truy cập trực tiếp)
        if (!$studentCode) {
            return redirect()->route('graduate.employment.verify')->withErrors(['message' => 'Phiên làm việc hết hạn hoặc không hợp lệ. Vui lòng xác thực lại.']);
        }

        // Validate dữ liệu form (giữ nguyên validation rules như trước)
        $validatedData = $request->validate([
            'employment_status' => ['required', 'string', 'in:Đã có việc làm,Chưa có việc làm,Đang học nâng cao,Khác'],
            'job_title' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'max:255'],
            'company_name' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'employment_type' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'in:Đúng ngành đào tạo,Trái ngành đào tạo'],
            'start_date' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'date'],
            'contract_type' => ['nullable', 'required_if:employment_status,Đã có việc làm', 'string', 'in:Hợp đồng xác định thời hạn,Hợp đồng không xác định thời hạn,Khác'],
            'is_teaching_related' => ['nullable', 'boolean'],
             // Trường teaching_location chỉ bắt buộc nếu is_teaching_related được check
            'teaching_location' => ['nullable', 'required_if:is_teaching_related,true', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:15'],
            'contact_address' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        // Thêm student_code và declaration_date
        $validatedData['student_code'] = $studentCode;
        $validatedData['declaration_date'] = Carbon::now();
        // Xử lý giá trị checkbox 'is_teaching_related' (nếu không check sẽ không gửi giá trị)
        $validatedData['is_teaching_related'] = $request->has('is_teaching_related');

        // Tìm hoặc tạo mới bản ghi, sau đó cập nhật/lưu
        GraduateEmployment::updateOrCreate(
            ['student_code' => $studentCode], // Điều kiện tìm kiếm
            $validatedData // Dữ liệu để cập nhật hoặc tạo mới
        );

        // Xóa session sau khi lưu thành công
        Session::forget('verified_student_code');

        // Chuyển hướng về trang xác thực ban đầu với thông báo thành công
        return redirect()->route('graduate.employment.verify')->with('status', 'Khai báo/Cập nhật thông tin việc làm thành công!');
    }
}