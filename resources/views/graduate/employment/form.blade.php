{{-- resources/views/graduate/employment/form.blade.php --}}
<x-public-form-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            KHAI BÁO THÔNG TIN VIỆC LÀM SAU TỐT NGHIỆP
        </h2>
    </div>
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('graduate.employment.store') }}">
                        @csrf
                        {{-- Phần thông tin sinh viên (Giữ nguyên) --}}
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Thông tin Sinh viên</h3>
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
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Ngành</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $student->class?->major?->major_name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Thông tin Việc làm & Liên hệ</h3>

                        <div class="mt-4">
                            <x-input-label for="employment_status" value="Tình trạng việc làm hiện tại (*)" />
                            <select id="employment_status" name="employment_status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required onchange="toggleEmploymentFields()">
                                <option value="" disabled {{ old('employment_status', $employmentInfo->employment_status ?? '') == '' ? 'selected' : '' }}>-- Chọn tình trạng --</option>
                                <option value="Đã có việc làm" {{ old('employment_status', $employmentInfo->employment_status ?? '') == 'Đã có việc làm' ? 'selected' : '' }}>Đã có việc làm</option>
                                <option value="Chưa có việc làm" {{ old('employment_status', $employmentInfo->employment_status ?? '') == 'Chưa có việc làm' ? 'selected' : '' }}>Chưa có việc làm</option>
                                <option value="Đang học nâng cao" {{ old('employment_status', $employmentInfo->employment_status ?? '') == 'Đang học nâng cao' ? 'selected' : '' }}>Đang học nâng cao</option>
                                <option value="Khác" {{ old('employment_status', $employmentInfo->employment_status ?? '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                            <x-input-error :messages="$errors->get('employment_status')" class="mt-2" />
                        </div>

                        <div id="employment_details" class="{{ old('employment_status', $employmentInfo->employment_status ?? '') == 'Đã có việc làm' ? '' : 'hidden' }}">
                            {{-- ... (Các trường job_title, company_name, company_address, employment_type, start_date, contract_type giữ nguyên) ... --}}
                             <div class="mt-4">
                                <x-input-label for="job_title" value="Chức danh/Vị trí công việc (*)" />
                                <x-text-input id="job_title" class="block mt-1 w-full" type="text" name="job_title" :value="old('job_title', $employmentInfo->job_title ?? '')" />
                                <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="company_name" value="Tên cơ quan/Công ty (*)" />
                                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name', $employmentInfo->company_name ?? '')" />
                                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                            </div>
                            
                            <div class="mt-4">
                                <x-input-label for="company_address" value="Địa chỉ cơ quan/Công ty" />
                                <textarea id="company_address" name="company_address" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('company_address', $employmentInfo->company_address ?? '') }}</textarea>
                                <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="company_phone" value="Số điện thoại cơ quan" />
                                <x-text-input id="company_phone" class="block mt-1 w-full" type="text" name="company_phone" :value="old('company_phone', $employmentInfo->company_phone ?? '')" />
                                <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
                            </div>
                             <div class="mt-4">
                                <x-input-label for="employment_type" value="Loại hình công việc (*)" />
                                <select id="employment_type" name="employment_type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="" disabled {{ old('employment_type', $employmentInfo->employment_type ?? '') == '' ? 'selected' : '' }}>-- Chọn loại hình --</option>
                                    <option value="Đúng ngành đào tạo" {{ old('employment_type', $employmentInfo->employment_type ?? '') == 'Đúng ngành đào tạo' ? 'selected' : '' }}>Đúng ngành đào tạo</option>
                                    <option value="Trái ngành đào tạo" {{ old('employment_type', $employmentInfo->employment_type ?? '') == 'Trái ngành đào tạo' ? 'selected' : '' }}>Trái ngành đào tạo</option>
                                </select>
                                <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
                            </div>

                             <div class="mt-4">
                                <x-input-label for="start_date" value="Ngày bắt đầu làm việc (*)" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $employmentInfo->start_date ?? '')" />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                             <div class="mt-4">
                                <x-input-label for="contract_type" value="Loại hợp đồng (*)" />
                                <select id="contract_type" name="contract_type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="" disabled {{ old('contract_type', $employmentInfo->contract_type ?? '') == '' ? 'selected' : '' }}>-- Chọn loại hợp đồng --</option>
                                    <option value="Hợp đồng xác định thời hạn" {{ old('contract_type', $employmentInfo->contract_type ?? '') == 'Hợp đồng xác định thời hạn' ? 'selected' : '' }}>Hợp đồng xác định thời hạn</option>
                                    <option value="Hợp đồng không xác định thời hạn" {{ old('contract_type', $employmentInfo->contract_type ?? '') == 'Hợp đồng không xác định thời hạn' ? 'selected' : '' }}>Hợp đồng không xác định thời hạn</option>
                                    <option value="Khác" {{ old('contract_type', $employmentInfo->contract_type ?? '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                                </select>
                                <x-input-error :messages="$errors->get('contract_type')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <label for="is_teaching_related" class="inline-flex items-center">
                                    <input id="is_teaching_related" type="checkbox" name="is_teaching_related" value="1" {{ old('is_teaching_related', $employmentInfo->is_teaching_related ?? false) ? 'checked' : '' }} class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" onchange="toggleTeachingLocation()">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Công việc thuộc ngành giáo dục/địa phương theo NĐ 116?</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_teaching_related')" class="mt-2" />
                            </div>
                            
                            {{-- THAY ĐỔI 1: ĐỊA PHƯƠNG CÔNG TÁC --}}
                            <div id="teaching_location_div" class="mt-4 {{ old('is_teaching_related', $employmentInfo->is_teaching_related ?? false) ? '' : 'hidden' }}">
                                <x-input-label for="teaching_province_code" value="Địa phương công tác (Tỉnh/Thành phố) (*)" />
                                <select id="teaching_province_code" name="teaching_province_code" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->code }}" {{ old('teaching_province_code', $employmentInfo->teaching_province_code ?? '') == $province->code ? 'selected' : '' }}>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('teaching_province_code')" class="mt-2" />
                            </div>

                        </div> 

                        {{-- THAY ĐỔI 2: ĐỊA CHỈ LIÊN HỆ --}}
                        <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Thông tin Liên hệ (Cập nhật nếu thay đổi)</h3>
                            
                            {{-- Grid 2 cột cho Email và SĐT --}}
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <div>
                                    <x-input-label for="contact_email" value="Email liên hệ (*)" />
                                    <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email', $employmentInfo->contact_email ?? '')" required />
                                    <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="contact_phone" value="Số điện thoại liên hệ (*)" />
                                    <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone', $employmentInfo->contact_phone ?? '')" required />
                                    <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Grid 2 cột cho Tỉnh và Xã/Phường --}}
                             <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <div>
                                    <x-input-label for="contact_province_code" value="Tỉnh/Thành phố (*)" />
                                    <select id="contact_province_code" name="contact_province_code" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->code }}" {{ old('contact_province_code', $employmentInfo->contact_province_code ?? '') == $province->code ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('contact_province_code')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="contact_ward_code" value="Xã/Phường (*)" />
                                    <select id="contact_ward_code" name="contact_ward_code" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">-- Chọn Xã/Phường --</option>
                                        {{-- $wards được truyền từ controller --}}
                                        @foreach ($wards as $ward) 
                                             <option value="{{ $ward->code }}" {{ old('contact_ward_code', $employmentInfo->contact_ward_code ?? '') == $ward->code ? 'selected' : '' }}>
                                                {{ $ward->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('contact_ward_code')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Trường chi tiết Số nhà, đường, thôn... --}}
                            <div class="mt-4">
                                <x-input-label for="contact_address_detail" value="Số nhà, tên đường, thôn/xóm (*)" />
                                <x-text-input id="contact_address_detail" class="block mt-1 w-full" type="text" name="contact_address_detail" :value="old('contact_address_detail', $employmentInfo->contact_address_detail ?? '')" required />
                                <x-input-error :messages="$errors->get('contact_address_detail')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="notes" value="Ghi chú thêm" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes', $employmentInfo->notes ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        {{-- Nút bấm (Giữ nguyên) --}}
                        <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('graduate.employment.verify') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                « Quay lại trang xác thực
                            </a>
                            <x-primary-button>
                                {{ $employmentInfo && $employmentInfo->exists ? 'Cập nhật Khai báo' : 'Gửi Khai báo' }}
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- THAY ĐỔI 3: THÊM SCRIPT CHO CASCADING DROPDOWN --}}
                    <script>
                        // Script ẩn/hiện trường việc làm (Giữ nguyên)
                        function toggleEmploymentFields() {
                            const status = document.getElementById('employment_status').value;
                            const detailsDiv = document.getElementById('employment_details');
                            const jobTitle = document.getElementById('job_title');
                            const companyName = document.getElementById('company_name');
                            const employmentType = document.getElementById('employment_type');
                            const startDate = document.getElementById('start_date');
                            const contractType = document.getElementById('contract_type');
                            const teachingLocationDiv = document.getElementById('teaching_location_div');
                            const isTeachingCheckbox = document.getElementById('is_teaching_related');

                            const fieldsToRequire = [jobTitle, companyName, employmentType, startDate, contractType];

                            if (status === 'Đã có việc làm') {
                                detailsDiv.classList.remove('hidden');
                                fieldsToRequire.forEach(field => field ? field.required = true : null);
                                toggleTeachingLocation();
                            } else {
                                detailsDiv.classList.add('hidden');
                                fieldsToRequire.forEach(field => field ? field.required = false : null);
                                if (isTeachingCheckbox) {
                                    isTeachingCheckbox.checked = false; 
                                    toggleTeachingLocation(); 
                                }
                            }
                        }

                        function toggleTeachingLocation() {
                            const isTeachingCheckbox = document.getElementById('is_teaching_related');
                            const teachingLocationDiv = document.getElementById('teaching_location_div');
                            const teachingLocationInput = document.getElementById('teaching_province_code'); // Sửa thành teaching_province_code

                            if (isTeachingCheckbox && teachingLocationDiv && teachingLocationInput) {
                                if (isTeachingCheckbox.checked) {
                                    teachingLocationDiv.classList.remove('hidden');
                                    teachingLocationInput.required = true; 
                                } else {
                                    teachingLocationDiv.classList.add('hidden');
                                    teachingLocationInput.required = false; 
                                }
                            }
                        }

                        // SCRIPT MỚI CHO CASCADING DROPDOWN
                        document.getElementById('contact_province_code').addEventListener('change', function() {
                            const provinceCode = this.value;
                            const wardSelect = document.getElementById('contact_ward_code');

                            // Xóa các option cũ
                            wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';

                            if (provinceCode) {
                                // Gọi API
                                fetch(`/api/get-wards-by-province/${provinceCode}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>'; // Reset
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

                        // Gọi lần đầu khi load trang
                        document.addEventListener('DOMContentLoaded', function() {
                            toggleEmploymentFields();
                            // Không cần trigger change cho province, vì controller đã load $wards ban đầu
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-public-form-layout>