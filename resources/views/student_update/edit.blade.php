<x-public-form-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Cập nhật Thông tin Cá nhân
        </h2>
    </div>

    <form method="POST" action="{{ route('student.update.store') }}">
        @csrf
        
        {{-- THÔNG TIN CỐ ĐỊNH (KHÔNG SỬA) --}}
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Thông tin sinh viên </h3>
        <div class="mt-2 mb-6 border-t border-gray-100 dark:border-gray-700">
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Họ và tên</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $student->full_name }}</dd>
                </div>
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Mã sinh viên</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $student->student_code }}</dd>
                </div>
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Lớp</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $student->class?->class_name ?? 'N/A' }}</dd>
                </div>
                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Trạng thái</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $student->status }}</dd>
                </div>
            </dl>
        </div>


        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Thông tin Kinh phí đã nhận</h3>
        <div class="mt-2 mb-6 border-t border-gray-100 dark:border-gray-700">
             <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Tổng hỗ trợ Học phí</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0 font-semibold text-green-600 dark:text-green-400">
    
                        @php
                            echo number_format($totalTuitionGrant ?? 0, 0, ',', '.') . ' VNĐ';
                        @endphp
                    </dd>
                </div>
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Tổng hỗ trợ Sinh hoạt phí</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0 font-semibold text-green-600 dark:text-green-400">
                        @php
                            echo number_format($totalLivingAllowance ?? 0, 0, ',', '.') . ' VNĐ';
                        @endphp
                    </dd>
                </div>
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0 bg-gray-50 dark:bg-gray-700/50">
                    <dt class="text-sm font-bold leading-6 text-gray-900 dark:text-gray-100">TỔNG CỘNG ĐÃ NHẬN</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-100 sm:col-span-2 sm:mt-0 font-bold text-lg text-blue-600 dark:text-blue-400">
                        @php
                            $tongCong = ($totalTuitionGrant ?? 0) + ($totalLivingAllowance ?? 0);
                            echo number_format($tongCong, 0, ',', '.') . ' VNĐ';
                        @endphp
                    </dd>
                </div>
            </dl>
        </div>
    

        {{-- THÔNG TIN ĐƯỢC PHÉP SỬA --}}
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Cập nhật thông tin cá nhân</h3>
        
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 ">
            
            {{-- Email --}}
            <div>
                <x-input-label for="email" value="Email liên hệ (*)" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full {{ $inputsDisabled ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}" 
                    type="email" 
                    name="email" 
                    :value="old('email', $student->email)" 
                    required 
                    :disabled="$inputsDisabled" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- SĐT --}}
            <div>
                <x-input-label for="phone" value="Số điện thoại liên hệ (*)" />
                <x-text-input 
                    id="phone" 
                    class="block mt-1 w-full {{ $inputsDisabled ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}" 
                    type="text" 
                    name="phone" 
                    :value="old('phone', $student->phone)" 
                    required 
                    :disabled="$inputsDisabled" 
                />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            
            {{-- Địa chỉ liên hệ --}}
            <div class="md:col-span-2 mt-4 border-t pt-4">
                <p class="text-base font-medium text-gray-900 dark:text-gray-100">Địa chỉ liên hệ (*)</p>
            </div>
            
             {{-- Tỉnh/Thành phố --}}
             <div>
                <x-input-label for="province_code" value="Tỉnh/Thành phố (*)" />
                <select 
                    id="province_code" 
                    name="province_code" 
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm {{ $inputsDisabled ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                    required 
                    @disabled($inputsDisabled)
                >
                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->code }}" {{ old('province_code', $student->province_code) == $province->code ? 'selected' : '' }}>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('province_code')" class="mt-2" />
            </div>
            
            {{-- Xã/Phường --}}
            <div>
                <x-input-label for="ward_code" value="Xã/Phường (*)" />
                <select 
                    id="ward_code" 
                    name="ward_code" 
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm {{ $inputsDisabled ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                    required 
                    @disabled($inputsDisabled)
                >
                    <option value="">-- Chọn Xã/Phường --</option>
                    @foreach ($wards as $ward) 
                        <option value="{{ $ward->code }}" {{ old('ward_code', $student->ward_code) == $ward->code ? 'selected' : '' }}>
                            {{ $ward->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('ward_code')" class="mt-2" />
            </div>
            
            {{-- Số nhà/Đường --}}
            <div class="md:col-span-2">
                <x-input-label for="address_detail" value="Số nhà, tên đường, thôn/xóm (*)" />
                <x-text-input 
                    id="address_detail" 
                    class="block mt-1 w-full {{ $inputsDisabled ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}" 
                    type="text" 
                    name="address_detail" 
                    :value="old('address_detail', $student->address_detail)" 
                    required 
                    :disabled="$inputsDisabled" 
                />
                <x-input-error :messages="$errors->get('address_detail')" class="mt-2" />
            </div>

        </div> 
        
        {{-- Nút Lưu / Thông báo --}}
        @if (!$inputsDisabled)
            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('student.update.verify') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    « Hủy (Quay lại)
                </a>
                <x-primary-button>
                    Lưu Cập Nhật
                </x-primary-button>
            </div>
        @else
             <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-right">
                 <a href="{{ route('student.update.verify') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">« Quay lại</a>
                 <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400 font-bold">
                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Chức năng cập nhật đang tạm đóng. Bạn chỉ có thể xem thông tin.
                </p>
             </div>
        @endif

    </form>

    <script>
        document.getElementById('province_code').addEventListener('change', function() {
            const provinceCode = this.value;
            const wardSelect = document.getElementById('ward_code');

            // Nếu input bị disabled thì không chạy logic này
            if (this.disabled) return;

            wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';

            if (provinceCode) {
                fetch(`/api/get-wards-by-province/${provinceCode}`) 
                    .then(response => response.json())
                    .then(data => {
                        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>'; 
                        data.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.code;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy danh sách xã/phường:', error);
                        wardSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                    });
            } else {
                wardSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố trước --</option>';
            }
        });
    </script>

</x-public-form-layout>