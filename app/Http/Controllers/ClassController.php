<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Major;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassModel::with(['major', 'faculty']);
        
        // Tìm kiếm
        if ($request->has('search')) {
            $query->where('class_name', 'like', '%' . $request->search . '%')
                  ->orWhere('class_code', 'like', '%' . $request->search . '%');
        }

        $classes = $query->orderBy('id', 'desc')->paginate(10);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $majors = Major::with('faculty')->get(); // Lấy ngành kèm khoa để hiển thị
        return view('classes.create', compact('majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_code' => 'required|unique:116_classes,class_code',
            'class_name' => 'required',
            'major_id' => 'required|exists:116_majors,id',
            'course_year' => 'required|integer',
            'class_size' => 'integer|min:0',
            'class_status' => 'required|in:Đang học,Đã huỷ,Đã tốt nghiệp',
        ]);

        $data = $request->all();
        
        // Tự động lấy faculty_id từ major đã chọn
        $major = Major::find($request->major_id);
        $data['faculty_id'] = $major->faculty_id;

        ClassModel::create($data);

        return redirect()->route('classes.index')->with('success', 'Thêm lớp thành công');
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        $majors = Major::all();
        return view('classes.edit', compact('class', 'majors'));
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);
        
        $request->validate([
            'class_code' => 'required|unique:116_classes,class_code,'.$id,
            'class_name' => 'required',
            'major_id' => 'required|exists:116_majors,id',
            'course_year' => 'required|integer',
            'class_status' => 'required',
            'class_size' => 'nullable|integer|min:0', // Thêm validate cho sĩ số nếu chưa có
        ]);

        // SỬA LỖI Ở ĐÂY: Dùng except() thay vì all() để loại bỏ _token và _method
        $data = $request->except(['_token', '_method']);
        
        // Tự động lấy faculty_id từ major đã chọn
        $major = Major::find($request->major_id);
        if ($major) {
            $data['faculty_id'] = $major->faculty_id;
        }

        $class->update($data);

        return redirect()->route('classes.index')->with('success', 'Cập nhật lớp thành công');
    }

    public function destroy($id)
    {
        ClassModel::destroy($id);
        return redirect()->route('classes.index')->with('success', 'Xóa lớp thành công');
    }
}