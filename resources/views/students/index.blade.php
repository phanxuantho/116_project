{{-- Kế thừa cấu trúc từ file layout chính `layouts.app` --}}
<x-app-layout>
    {{-- Định nghĩa nội dung cho phần header của layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Danh sách Sinh viên') }}
        </h2>
    </x-slot>

    {{-- Phần nội dung chính của trang sẽ được đặt ở đây --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Form Bộ lọc -->
            <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
                <form action="{{ route('students.index') }}" method="GET">
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
            
            {{-- Hiển thị thông báo thành công --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

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
                                {{-- THÊM MỚI: Cột hành động --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
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
                                    {{-- THÊM MỚI: Nút Sửa --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('students.edit', $student->student_code) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-gray-500">Không tìm thấy sinh viên nào phù hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <!-- Phân trang -->
                <div class="p-4 bg-white border-t border-gray-200">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT: Xử lý bộ lọc động cho Lớp học --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const facultyFilter = document.getElementById('faculty-filter');
            const courseFilter = document.getElementById('course-filter');
            const classFilter = document.getElementById('class-filter');
            
            const originalClassOptionsHTML = classFilter.innerHTML;

            function updateClassOptions() {
                const facultyId = facultyFilter.value;
                const courseYear = courseFilter.value;

                if (!facultyId && !courseYear) {
                    classFilter.innerHTML = originalClassOptionsHTML;
                    const selectedClassId = '{{ $filters['class_id'] ?? '' }}';
                    if(selectedClassId) {
                        classFilter.value = selectedClassId;
                    }
                    return;
                }

                const params = new URLSearchParams();
                if (facultyId) {
                    params.append('faculty_id', facultyId);
                }
                if (courseYear) {
                    params.append('course_year', courseYear);
                }
                
                const url = `{{ route('api.get_classes') }}?${params.toString()}`;

                fetch(url)
                    .then(response => response.json())
                    .then(classes => {
                        const selectedClassId = '{{ $filters['class_id'] ?? '' }}';
                        classFilter.innerHTML = '<option value="">Tất cả Lớp</option>';
                        let isSelectedClassStillAvailable = false;

                        classes.forEach(cls => {
                            const option = document.createElement('option');
                            option.value = cls.id;
                            option.textContent = cls.class_name;
                            if (cls.id == selectedClassId) {
                                option.selected = true;
                                isSelectedClassStillAvailable = true;
                            }
                            classFilter.appendChild(option);
                        });

                        if (!isSelectedClassStillAvailable) {
                            classFilter.value = "";
                        }
                    })
                    .catch(error => console.error('Lỗi khi tải danh sách lớp:', error));
            }

            facultyFilter.addEventListener('change', updateClassOptions);
            courseFilter.addEventListener('change', updateClassOptions);
            
            updateClassOptions();
        });
    </script>
</x-app-layout>