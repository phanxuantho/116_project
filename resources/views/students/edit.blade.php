<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cập nhật thông tin sinh viên: {{ $student->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- FORM CẬP NHẬT --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Vui lòng kiểm tra lại lỗi!</p>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('students.update', $student->student_code) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        {{-- THÊM MỚI: Input ẩn để lưu lại chuỗi query của bộ lọc --}}
                        <input type="hidden" name="previous_url_query" value="{{ $previousUrlQuery ?? '' }}">
                        
                        <h3 class="text-lg font-semibold border-b pb-2 mb-6">Thông tin cá nhân</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Cột 1 -->
                            <div class="space-y-6">
                                <div>
                                    <label for="student_code" class="block font-medium text-sm text-gray-700">Mã Sinh viên</label>
                                    <input id="student_code" class="block p-2 w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm" type="text" value="{{ $student->student_code }}" disabled readonly>
                                </div>
                                <div>
                                    <label for="full_name" class="block font-medium text-sm text-gray-700">Họ và Tên</label>
                                    <input id="full_name" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required autofocus>
                                </div>
                                <div>
                                    <label for="dob" class="block font-medium text-sm text-gray-700">Ngày sinh</label>
                                    <input id="dob" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="date" name="dob" value="{{ old('dob', $student->dob) }}" required>
                                </div>
                                <div>
                                    <label for="gender" class="block font-medium text-sm text-gray-700">Giới tính</label>
                                    <select id="gender" name="gender" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm">
                                        <option value="Nam" {{ old('gender', $student->gender) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                        <option value="Nữ" {{ old('gender', $student->gender) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                        <option value="Khác" {{ old('gender', $student->gender) == 'Khác' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Cột 2 -->
                            <div class="space-y-6">
                                <div>
                                    <label for="citizen_id_card" class="block font-medium text-sm text-gray-700">Số CCCD</label>
                                    <input id="citizen_id_card" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="citizen_id_card" value="{{ old('citizen_id_card', $student->citizen_id_card) }}" required>
                                </div>
                                <div>
                                    <label for="phone" class="block font-medium text-sm text-gray-700">Số điện thoại</label>
                                    <input id="phone" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="phone" value="{{ old('phone', $student->phone) }}">
                                </div>
                                <div>
                                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                                    <input id="email" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="email" name="email" value="{{ old('email', $student->email) }}">
                                </div>
                            </div>

                            <!-- Cột 3 -->
                            <div class="space-y-6">
                                <div>
                                    <label for="bank_name" class="block font-medium text-sm text-gray-700">Tên Ngân hàng</label>
                                    <input id="bank_name" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="bank_name" value="{{ old('bank_name', $student->bank_name) }}">
                                </div>
                                <div>
                                    <label for="bank_branch" class="block font-medium text-sm text-gray-700">Chi nhánh</label>
                                    <input id="bank_branch" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="bank_branch" value="{{ old('bank_branch', $student->bank_branch) }}">
                                </div>
                                <div>
                                    <label for="bank_account" class="block font-medium text-sm text-gray-700">Số tài khoản</label>
                                    <input id="bank_account" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="bank_account" value="{{ old('bank_account', $student->bank_account) }}">
                                </div>
                            </div>

                            <!-- Địa chỉ -->
                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="province_code" class="block font-medium text-sm text-gray-700">Tỉnh/Thành phố</label>
                                    <select id="province_code" name="province_code" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->code }}" {{ old('province_code', $student->province_code) == $province->code ? 'selected' : '' }}>
                                                {{ $province->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="ward_code" class="block font-medium text-sm text-gray-700">Xã/Phường</label>
                                    <select id="ward_code" name="ward_code" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Chọn Xã/Phường --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label for="address_detail" class="block font-medium text-sm text-gray-700">Địa chỉ chi tiết (Số nhà, đường, thôn...)</label>
                                <textarea id="address_detail" name="address_detail" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" rows="2">{{ old('address_detail', $student->address_detail) }}</textarea>
                            </div>

                            <div class="md:col-span-3">
                                <label for="old_address_detail" class="block font-medium text-sm text-gray-700">Địa chỉ cũ (nếu có)</label>
                                <textarea id="old_address_detail" name="old_address_detail" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" rows="2">{{ old('old_address_detail', $student->old_address_detail) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-8">

                        <h3 class="text-lg font-semibold border-b pb-2 mb-6">Thông tin Học vụ & Hỗ trợ</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="class_id" class="block font-medium text-sm text-gray-700">Lớp</label>
                                <select id="class_id" name="class_id" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" required>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->class_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700">Trạng thái học</label>
                                <select id="status" name="status" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Đang học" {{ old('status', $student->status) == 'Đang học' ? 'selected' : '' }}>Đang học</option>
                                    <option value="Bảo lưu" {{ old('status', $student->status) == 'Bảo lưu' ? 'selected' : '' }}>Bảo lưu</option>
                                    <option value="Tốt nghiệp" {{ old('status', $student->status) == 'Tốt nghiệp' ? 'selected' : '' }}>Tốt nghiệp</option>
                                    <option value="Thôi học" {{ old('status', $student->status) == 'Thôi học' ? 'selected' : '' }}>Thôi học</option>
                                </select>
                            </div>
                            <div>
                                <label for="funding_status" class="block font-medium text-sm text-gray-700">Trạng thái nhận hỗ trợ</label>
                                <select id="funding_status" name="funding_status" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Đang nhận" {{ old('funding_status', $student->funding_status) == 'Đang nhận' ? 'selected' : '' }}>Đang nhận</option>
                                    <option value="Tạm dừng nhận" {{ old('funding_status', $student->funding_status) == 'Tạm dừng nhận' ? 'selected' : '' }}>Tạm dừng nhận</option>
                                    <option value="Thôi nhận" {{ old('funding_status', $student->funding_status) == 'Thôi nhận' ? 'selected' : '' }}>Thôi nhận</option>
                                    <option value="Gia hạn" {{ old('funding_status', $student->funding_status) == 'Gia hạn' ? 'selected' : '' }}>Gia hạn</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-8">

                        <h3 class="text-lg font-semibold border-b pb-2 mb-6">Ghi chú và Minh chứng (Nếu có thay đổi trạng thái)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="evidence" class="block font-medium text-sm text-gray-700">Loại minh chứng</label>
                                <input id="evidence" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="text" name="evidence" value="{{ old('evidence') }}" placeholder="VD: Quyết định, Đơn xin...">
                            </div>
                            <div>
                                <label for="evidence_date" class="block font-medium text-sm text-gray-700">Ngày minh chứng</label>
                                <input id="evidence_date" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" type="date" name="evidence_date" value="{{ old('evidence_date') }}">
                            </div>
                            <div class="md:col-span-2">
                                <label for="note" class="block font-medium text-sm text-gray-700">Ghi chú (Lý do thay đổi)</label>
                                <textarea id="note" name="note" class="block p-2 w-full border border-gray-300 rounded-md shadow-sm" rows="3">{{ old('note') }}</textarea>
                            </div>
                             <div class="md:col-span-2">
                                <label for="evidence_file" class="block font-medium text-sm text-gray-700">Tệp minh chứng (PDF, JPG, PNG)</label>
                                <input id="evidence_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="evidence_file">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                Hủy
                            </a>
                            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- BẢNG LỊCH SỬ CẬP NHẬT --}}
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-semibold border-b pb-2 mb-6">Lịch sử Cập nhật Trạng thái</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày Cập nhật</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người Cập nhật</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái Học</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái HT</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Minh chứng</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($student->statusLogs as $log)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $log->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if($log->status_old)
                                                <span class="text-gray-500">{{ $log->status_old }}</span> → 
                                            @endif
                                            <span class="font-semibold">{{ $log->status_new }}</span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if($log->funding_status_old)
                                                <span class="text-gray-500">{{ $log->funding_status_old }}</span> → 
                                            @endif
                                            <span class="font-semibold">{{ $log->funding_status_new }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm">{{ $log->note }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if ($log->evidence_file_path)
                                                <a href="{{ Storage::url($log->evidence_file_path) }}" target="_blank" class="text-blue-600 hover:underline">Xem tệp</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-gray-500">Chưa có lịch sử cập nhật.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_code');
            const wardSelect = document.getElementById('ward_code');
            
            function fetchWards(provinceCode, selectedWardCode = null) {
                if (!provinceCode) {
                    wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                    return;
                }
                const url = `{{ route('api.get_wards') }}?province_code=${provinceCode}`;
                fetch(url)
                    .then(response => response.json())
                    .then(wards => {
                        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                        wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.code;
                            option.textContent = ward.full_name;
                            if (ward.code === selectedWardCode) {
                                option.selected = true;
                            }
                            wardSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Lỗi khi tải danh sách xã/phường:', error));
            }

            provinceSelect.addEventListener('change', function() {
                fetchWards(this.value);
            });

            const initialProvinceCode = '{{ old('province_code', $student->province_code) }}';
            const initialWardCode = '{{ old('ward_code', $student->ward_code) }}';
            if (initialProvinceCode) {
                fetchWards(initialProvinceCode, initialWardCode);
            }
        });
    </script>
</x-app-layout>