<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
{
    /**
     * Hiển thị danh sách khoa.
     */
    public function index(Request $request)
    {
        $query = Faculty::query();

        // Tìm kiếm theo mã hoặc tên khoa
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('faculty_name', 'like', '%' . $search . '%')
                  ->orWhere('faculty_code', 'like', '%' . $search . '%');
            });
        }

        // Sắp xếp và phân trang
        $faculties = $query->orderBy('faculty_name', 'asc')->paginate(10);
        $faculties->appends(['search' => $request->search]);

        return view('faculties.index', compact('faculties'));
    }

    /**
     * Hiển thị form thêm mới.
     */
    public function create()
    {
        return view('faculties.create');
    }

    /**
     * Lưu khoa mới.
     */
    public function store(Request $request)
    {
        // --- BẮT ĐẦU ĐOẠN KIỂM TRA DỮ LIỆU ---
        $request->validate([
            // unique:tên_bảng,tên_cột -> kiểm tra trùng lặp
            'faculty_code' => ['required', 'string', 'max:20', 'unique:116_faculties,faculty_code'],
            'faculty_name' => ['required', 'string', 'max:255'],
        ], [
            'faculty_code.required' => 'Vui lòng nhập mã khoa.',
            'faculty_code.unique' => 'Mã khoa này đã tồn tại trong hệ thống.', // Thông báo lỗi tùy chỉnh
            'faculty_name.required' => 'Vui lòng nhập tên khoa.',
        ]);
        // --- KẾT THÚC ĐOẠN KIỂM TRA DỮ LIỆU ---

        // Nếu dữ liệu hợp lệ (không trùng), mới chạy lệnh tạo này
        Faculty::create($request->except(['_token']));

        return redirect()->route('faculties.index')->with('success', 'Thêm khoa thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa.
     */
    public function edit($id)
    {
        $faculty = Faculty::findOrFail($id);
        return view('faculties.edit', compact('faculty'));
    }

    /**
     * Cập nhật thông tin khoa.
     */
 public function update(Request $request, $id)
    {
        $faculty = Faculty::findOrFail($id);

        $request->validate([
            'faculty_code' => ['required', 'string', 'max:20', Rule::unique('116_faculties', 'faculty_code')->ignore($faculty->id)],
            'faculty_name' => ['required', 'string', 'max:255'],
        ], [
            'faculty_code.required' => 'Vui lòng nhập mã khoa.',
            'faculty_code.unique' => 'Mã khoa này đã tồn tại.',
            'faculty_name.required' => 'Vui lòng nhập tên khoa.',
        ]);

        // SỬA LỖI TẠI ĐÂY: Loại bỏ _token và _method trước khi cập nhật
        $faculty->update($request->except(['_token', '_method']));

        return redirect()->route('faculties.index')->with('success', 'Cập nhật thông tin khoa thành công.');
    }

    /**
     * Xóa khoa.
     */
    public function destroy($id)
    {
        $faculty = Faculty::findOrFail($id);

        // Kiểm tra ràng buộc: Không xóa nếu khoa đang có ngành trực thuộc
        // (Giả định model Faculty có quan hệ majors())
        if ($faculty->majors()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa khoa này vì đang có ngành đào tạo trực thuộc.');
        }

        try {
            $faculty->delete();
            return redirect()->route('faculties.index')->with('success', 'Đã xóa khoa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa dữ liệu.');
        }
    }
}