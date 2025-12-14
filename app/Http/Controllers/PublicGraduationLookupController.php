<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

class PublicGraduationLookupController extends Controller
{
    public function index(Request $request)
    {
        // 1. Chỉ lấy các Tỉnh CÓ sinh viên đã tốt nghiệp
        // Logic: Tìm các sinh viên có quan hệ 'graduation', lấy mã tỉnh, loại bỏ trùng lặp
        $provinceCodes = Student::has('graduation')
            ->whereNotNull('province_code')
            ->distinct()
            ->pluck('province_code');

        $provinces = Province::whereIn('code', $provinceCodes)
            ->orderBy('name')
            ->get();

        $students = collect();
        $selectedProvince = null;

        // 2. Nếu người dùng đã chọn Tỉnh -> Thực hiện truy vấn
        if ($request->has('province_code') && $request->province_code != '') {
            
            $selectedProvince = Province::where('code', $request->province_code)->first();

            $students = Student::query()
                ->with(['graduation', 'province', 'ward', 'class']) // Thêm 'class' để lấy tên lớp
                ->whereHas('graduation') 
                ->where('province_code', $request->province_code)
                ->orderBy('student_code')
                ->get();
        }

        return view('public_lookup.graduation', compact('provinces', 'students', 'selectedProvince'));
    }
}