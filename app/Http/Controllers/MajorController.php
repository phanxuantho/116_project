<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    /**
     * Hiển thị danh sách ngành.
     */
    public function index(Request $request)
    {
        $query = Major::with('faculty'); // Eager load để lấy tên khoa

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('major_name', 'like', '%' . $search . '%')
                  ->orWhere('major_code', 'like', '%' . $search . '%');
            });
        }

        $majors = $query->orderBy('major_name', 'asc')->paginate(10);
        $majors->appends(['search' => $request->search]);

        return view('majors.index', compact('majors'));
    }

    /**
     * Form thêm mới.
     */
    public function create()
    {
        $faculties = Faculty::orderBy('faculty_name', 'asc')->get();
        return view('majors.create', compact('faculties'));
    }

    /**
     * Lưu ngành mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'major_code' => ['required', 'string', 'max:20', 'unique:116_majors,major_code'],
            'major_name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'exists:116_faculties,id'],
        ], [
            'major_code.required' => 'Vui lòng nhập mã ngành.',
            'major_code.unique' => 'Mã ngành này đã tồn tại.',
            'faculty_id.required' => 'Vui lòng chọn khoa trực thuộc.',
        ]);

        // Dùng except('_token') để tránh lỗi MassAssignment với field _token
        Major::create($request->except('_token'));

        return redirect()->route('majors.index')->with('success', 'Thêm ngành đào tạo thành công.');
    }

    /**
     * Form chỉnh sửa.
     */
    public function edit($id)
    {
        $major = Major::findOrFail($id);
        $faculties = Faculty::orderBy('faculty_name', 'asc')->get();
        return view('majors.edit', compact('major', 'faculties'));
    }

    /**
     * Cập nhật ngành.
     */
    public function update(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        $request->validate([
            // Bỏ qua ID hiện tại khi kiểm tra trùng mã
            'major_code' => ['required', 'string', 'max:20', Rule::unique('116_majors', 'major_code')->ignore($major->id)],
            'major_name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'exists:116_faculties,id'],
        ], [
            'major_code.unique' => 'Mã ngành này đã tồn tại.',
        ]);

        // Dùng except(['_token', '_method']) để loại bỏ các trường không có trong DB
        $major->update($request->except(['_token', '_method']));

        return redirect()->route('majors.index')->with('success', 'Cập nhật thông tin ngành thành công.');
    }

    /**
     * Xóa ngành.
     */
    public function destroy($id)
    {
        $major = Major::findOrFail($id);

        // Kiểm tra ràng buộc: Không xóa nếu ngành đang có lớp học
        if ($major->classes()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa ngành này vì đang có lớp học trực thuộc.');
        }

        $major->delete();
        return redirect()->route('majors.index')->with('success', 'Đã xóa ngành đào tạo.');
    }
}