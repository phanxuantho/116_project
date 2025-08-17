<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('In và Xuất Danh sách Rà soát Hàng tháng') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    {{-- Hiển thị lỗi validation từ server --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Không thể thực hiện. Vui lòng kiểm tra lại:</p>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Khối hiển thị lỗi từ JavaScript --}}
                    <div id="form-errors" class="hidden mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        {{-- Nội dung lỗi sẽ được JS chèn vào đây --}}
                    </div>

                    <form id="review-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Tháng -->
                            <div>
                                <label for="month" class="block font-medium text-sm text-gray-700">Tháng</label>
                                <input id="month" name="month" type="number" min="1" max="12" value="{{ (int) date('m') }}" required class="block mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Học kỳ -->
                            <div>
                                <label for="semester" class="block font-medium text-sm text-gray-700">Học kỳ</label>
                                <input id="semester" name="semester" type="text" placeholder="Ví dụ: 1" required class="block mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Năm học -->
                            <div>
                                <label for="school_year" class="block font-medium text-sm text-gray-700">Năm học</label>
                                <input id="school_year" name="school_year" type="text" placeholder="Ví dụ: 2024 - 2025" required class="block mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Khoa -->
                            <div>
                                <label for="faculty_id" class="block font-medium text-sm text-gray-700">Khoa</label>
                                <select id="faculty_id" name="faculty_id" required class="block mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Chọn Khoa --</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}">{{ $faculty->faculty_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Lớp -->
                            <div>
                                <label for="class_id" class="block font-medium text-sm text-gray-700">Lớp</label>
                                <select id="class_id" name="class_id" required class="block mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Chọn Lớp --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" data-faculty="{{ $class->faculty_id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <button type="button" id="print-all-button" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                In Tất Cả
                            </button>
                            <button type="button" id="print-button" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                In
                            </button>
                            <button type="button" id="export-button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Xuất Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('review-form');
            const facultySelect = document.getElementById('faculty_id');
            const classSelect = document.getElementById('class_id');
            const printButton = document.getElementById('print-button');
            const exportButton = document.getElementById('export-button');
            const allClassOptions = Array.from(classSelect.options);
            const errorDiv = document.getElementById('form-errors');
            const printAllButton = document.getElementById('print-all-button'); // Nút mới



            facultySelect.addEventListener('change', function() {
                const selectedFacultyId = this.value;
                classSelect.innerHTML = '';
                classSelect.appendChild(allClassOptions[0]);

                allClassOptions.forEach(option => {
                    if (option.value && (selectedFacultyId === '' || option.dataset.faculty === selectedFacultyId)) {
                        classSelect.appendChild(option.cloneNode(true));
                    }
                });
            });
            
            // Hàm kiểm tra form trước khi gửi
            function validateForm() {
                errorDiv.classList.add('hidden'); // Ẩn thông báo lỗi cũ
                let isValid = true;
                let errorMessages = [];
                
                form.querySelectorAll('[required]').forEach(field => {
                    field.classList.remove('border-red-500'); // Xóa viền đỏ cũ
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500'); // Thêm viền đỏ cho trường bị lỗi
                        const label = document.querySelector(`label[for="${field.id}"]`);
                        const fieldName = label ? label.textContent : field.name;
                        errorMessages.push(`Vui lòng điền vào trường "${fieldName}".`);
                    }
                });

                if (!isValid) {
                    // Hiển thị thông báo lỗi cụ thể
                    errorDiv.innerHTML = '<p class="font-bold">Không thể thực hiện. Vui lòng kiểm tra lại:</p><ul class="mt-2 list-disc list-inside">' 
                                       + [...new Set(errorMessages)].map(msg => `<li>${msg}</li>`).join('') 
                                       + '</ul>';
                    errorDiv.classList.remove('hidden');
                }
                return isValid;
            }

            exportButton.addEventListener('click', function() {
                if (validateForm()) {
                    form.method = 'GET';
                    form.action = '{{ route("reports.monthly-review.export") }}';
                    form.submit();
                }
            });

            printButton.addEventListener('click', function() {
                if (validateForm()) {
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData).toString();
                    const printUrl = `{{ route('reports.monthly-review.print') }}?${params}`;
                    window.open(printUrl, '_blank');
                }
            });
             // THÊM MỚI: Xử lý sự kiện cho nút "In Tất Cả"
             printAllButton.addEventListener('click', function() {
                // Chỉ cần validate các trường chung
                // (Code validate form có thể được tái sử dụng ở đây)
                const formData = new FormData(form);
                const params = new URLSearchParams();
                params.append('month', formData.get('month'));
                params.append('semester', formData.get('semester'));
                params.append('school_year', formData.get('school_year'));
                
                const printUrl = `{{ route('reports.monthly-review.print-all') }}?${params.toString()}`;
                window.open(printUrl, '_blank');
            });           



        });
    </script>
</x-app-layout>
