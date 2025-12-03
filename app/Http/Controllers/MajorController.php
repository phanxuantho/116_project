<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    /**
     * Hiển thị danh sách các ngành.
     */
    public function index(Request $request)
    {
        // Khởi tạo query và eager load 'faculty' để giảm số lượng truy vấn (N+1 problem)
        $query = Major::with('faculty');

        // Xử lý tìm kiếm nếu có tham số 'search' trên URL
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('major_name', 'like', '%' . $search . '%')
                  ->orWhere('major_code', 'like', '%' . $search . '%');
            });
        }

        // Phân trang kết quả, 10 bản ghi mỗi trang, sắp xếp theo tên ngành
        $majors = $query->orderBy('major_name', 'asc')->paginate(10);

        // Giữ lại tham số tìm kiếm khi chuyển trang
        $majors->appends(['search' => $request->search]);

        return view('majors.index', compact('majors'));
    }

    /**
     * Hiển thị form tạo mới ngành.
     */
    public function create()
    {
        // Lấy danh sách tất cả các khoa để hiển thị trong dropdown chọn khoa
        $faculties = Faculty::orderBy('faculty_name', 'asc')->get();
        
        return view('majors.create', compact('faculties'));
    }

    /**
     * Lưu ngành mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'major_code' => ['required', 'string', 'max:20', 'unique:116_majors,major_code'],
            'major_name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'integer', 'exists:116_faculties,id'],
        ], [
            'major_code.required' => 'Vui lòng nhập mã ngành.',
            'major_code.unique' => 'Mã ngành này đã tồn tại.',
            'major_name.required' => 'Vui lòng nhập tên ngành.',
            'faculty_id.required' => 'Vui lòng chọn khoa trực thuộc.',
            'faculty_id.exists' => 'Khoa đã chọn không hợp lệ.',
        ]);

        // Tạo mới
        Major::create([
            'major_code' => $request->major_code,
            'major_name' => $request->major_name,
            'faculty_id' => $request->faculty_id,
        ]);

        return redirect()->route('majors.index')->with('success', 'Thêm ngành đào tạo thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa ngành.
     */
    public function edit(string $id)
    {
        $major = Major::findOrFail($id);
        $faculties = Faculty::orderBy('faculty_name', 'asc')->get();

        return view('majors.edit', compact('major', 'faculties'));
    }

    /**
     * Cập nhật thông tin ngành.
     */
    public function update(Request $request, string $id)
    {
        $major = Major::findOrFail($id);

        // Validate dữ liệu
        $request->validate([
            // Rule unique bỏ qua ID hiện tại để không báo lỗi khi giữ nguyên mã cũ
            'major_code' => ['required', 'string', 'max:20', Rule::unique('116_majors', 'major_code')->ignore($major->id)],
            'major_name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'integer', 'exists:116_faculties,id'],
        ], [
            'major_code.required' => 'Vui lòng nhập mã ngành.',
            'major_code.unique' => 'Mã ngành này đã tồn tại.',
            'major_name.required' => 'Vui lòng nhập tên ngành.',
            'faculty_id.required' => 'Vui lòng chọn khoa trực thuộc.',
        ]);

        // Cập nhật
        $major->update([
            'major_code' => $request->major_code,
            'major_name' => $request->major_name,
            'faculty_id' => $request->faculty_id,
        ]);

        return redirect()->route('majors.index')->with('success', 'Cập nhật thông tin ngành thành công.');
    }

    /**
     * Xóa ngành khỏi cơ sở dữ liệu.
     */
    public function destroy(string $id)
    {
        $major = Major::findOrFail($id);

        // Kiểm tra ràng buộc (Optional): Nếu ngành đã có lớp học thì không cho xóa
        // Bạn cần đảm bảo Model Major đã định nghĩa relationship classes()
        // if ($major->classes()->count() > 0) {
        //     return redirect()->back()->with('error', 'Không thể xóa ngành này vì đã có lớp học trực thuộc.');
        // }

        try {
            $major->delete();
            return redirect()->route('majors.index')->with('success', 'Đã xóa ngành đào tạo.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa dữ liệu. Có thể ngành này đang được sử dụng ở bảng khác.');
        }
    }
}