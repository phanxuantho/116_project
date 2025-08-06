<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Sinh viên</title>
    <!-- Tải Tailwind CSS từ CDN để tạo giao diện nhanh chóng -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tải font Inter từ Google Fonts cho đẹp hơn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-4 md:p-8">
        <header class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Danh sách Sinh viên</h1>
            <p class="text-gray-600 mt-2">Tra cứu và quản lý thông tin sinh viên một cách hiệu quả.</p>
        </header>

        <!-- Form Bộ lọc: Gửi dữ liệu bằng phương thức GET đến route 'students.index' -->
        <div class="bg-white p-6 rounded-xl shadow-md mb-8">
            <form action="{{ route('students.index') }}" method="GET">
                {{-- Cập nhật grid layout thành 5 cột cho màn hình lớn --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
                    
                    <!-- Lọc theo Khoa -->
                    <div>
                        <label for="faculty-filter" class="block text-sm font-medium text-gray-700 mb-1">Khoa</label>
                        <select name="faculty_id" id="faculty-filter" class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả Khoa</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ ($filters['faculty_id'] ?? '') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->faculty_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lọc theo Khóa -->
                    <div>
                        <label for="course-filter" class="block text-sm font-medium text-gray-700 mb-1">Khóa học</label>
                        <select name="course_year" id="course-filter" class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả Khóa</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->course_year }}" {{ ($filters['course_year'] ?? '') == $course->course_year ? 'selected' : '' }}>
                                    Khóa {{ $course->course_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lọc theo Lớp -->
                    <div>
                        <label for="class-filter" class="block text-sm font-medium text-gray-700 mb-1">Lớp</label>
                        <select name="class_id" id="class-filter" class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả Lớp</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? '') == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lọc theo Tình trạng học -->
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Tình trạng</label>
                        <select name="academic_status" id="status-filter" class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả</option>
                            <option value="Đang học" {{ ($filters['academic_status'] ?? '') == 'Đang học' ? 'selected' : '' }}>Đang học</option>
                            <option value="Bảo lưu" {{ ($filters['academic_status'] ?? '') == 'Bảo lưu' ? 'selected' : '' }}>Bảo lưu</option>
                            <option value="Tốt nghiệp" {{ ($filters['academic_status'] ?? '') == 'Tốt nghiệp' ? 'selected' : '' }}>Tốt nghiệp</option>
                            <option value="Thôi học" {{ ($filters['academic_status'] ?? '') == 'Thôi học' ? 'selected' : '' }}>Thôi học</option>
                        </select>
                    </div>

                    <!-- Nút Lọc -->
                    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 shadow-sm">
                        Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Bảng danh sách sinh viên -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã SV</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ và Tên</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Sinh</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->student_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $student->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->class->class_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($student->status == 'Đang học') bg-green-100 text-green-800
                                        @elseif($student->status == 'Bảo lưu') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $student->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-gray-500">Không tìm thấy sinh viên nào phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <!-- Phân trang: Laravel tự động tạo các liên kết phân trang -->
            <div class="p-4 bg-white border-t border-gray-200">
                {{ $students->links() }}
            </div>
        </div>
         <footer class="text-center mt-8 text-sm text-gray-500">
            <p>&copy; 2024 - Hệ thống Quản lý Sinh viên</p>
        </footer>
    </div>

    {{-- SCRIPT MỚI: Xử lý bộ lọc động cho Lớp học --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy các element của dropdown
            const facultyFilter = document.getElementById('faculty-filter');
            const courseFilter = document.getElementById('course-filter');
            const classFilter = document.getElementById('class-filter');
            
            // Lưu trữ danh sách lớp ban đầu để có thể khôi phục khi không có bộ lọc nào được chọn
            const originalClassOptionsHTML = classFilter.innerHTML;

            // Hàm để cập nhật danh sách các lớp
            function updateClassOptions() {
                const facultyId = facultyFilter.value;
                const courseYear = courseFilter.value;

                // Nếu cả hai bộ lọc đều trống, khôi phục lại danh sách lớp đầy đủ
                if (!facultyId && !courseYear) {
                    classFilter.innerHTML = originalClassOptionsHTML;
                    return;
                }

                // Xây dựng URL để gọi API lấy danh sách lớp đã lọc
                const params = new URLSearchParams();
                if (facultyId) {
                    params.append('faculty_id', facultyId);
                }
                if (courseYear) {
                    params.append('course_year', courseYear);
                }
                
                // Sử dụng route đã đặt tên để tạo URL
                const url = `{{ route('api.get_classes') }}?${params.toString()}`;

                // Gọi API bằng fetch
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(classes => {
                        // Lưu lại giá trị lớp đang được chọn (nếu có)
                        const selectedClassId = classFilter.value;
                        
                        // Xóa các lựa chọn hiện tại và thêm lại lựa chọn mặc định "Tất cả Lớp"
                        classFilter.innerHTML = '<option value="">Tất cả Lớp</option>';

                        let isSelectedClassStillAvailable = false;

                        // Thêm các lớp đã được lọc vào dropdown
                        classes.forEach(cls => {
                            const option = document.createElement('option');
                            option.value = cls.id;
                            option.textContent = cls.class_name;
                            
                            // Nếu lớp này đang được chọn trước đó, giữ lại lựa chọn
                            if (cls.id == selectedClassId) {
                                option.selected = true;
                                isSelectedClassStillAvailable = true;
                            }
                            classFilter.appendChild(option);
                        });
                        
                        // Nếu lớp đã chọn trước đó không còn trong danh sách mới, bỏ lựa chọn đó
                        if (!isSelectedClassStillAvailable) {
                            classFilter.value = "";
                        }
                    })
                    .catch(error => console.error('Lỗi khi tải danh sách lớp:', error));
            }

            // Gắn sự kiện 'change' cho bộ lọc Khoa và Khóa học
            facultyFilter.addEventListener('change', updateClassOptions);
            courseFilter.addEventListener('change', updateClassOptions);
        });
    </script>

</body>
</html>






