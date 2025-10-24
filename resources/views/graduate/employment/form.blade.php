{{-- resources/views/graduate/employment/form.blade.php --}}
<x-public-form-layout> <div class="mb-4 text-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Khai báo Thông tin Việc làm sau Tốt nghiệp
        </h2>
    </div>
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Không cần thông báo status ở đây vì sẽ redirect về trang verify --}}

                    <form method="POST" action="{{ route('graduate.employment.store') }}">
                        @csrf
                        {{-- Phần thông tin sinh viên giữ nguyên --}}
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
                                 {{-- <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">Ngày tốt nghiệp</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0"> Lấy từ bảng Graduation nếu có </dd>
                                </div> --}}
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

                            <div id="teaching_location_div" class="mt-4 {{ old('is_teaching_related', $employmentInfo->is_teaching_related ?? false) ? '' : 'hidden' }}">
                                <x-input-label for="teaching_location" value="Địa phương công tác (Tỉnh/Thành phố) (*)" />
                                <x-text-input id="teaching_location" class="block mt-1 w-full" type="text" name="teaching_location" :value="old('teaching_location', $employmentInfo->teaching_location ?? '')" />
                                <x-input-error :messages="$errors->get('teaching_location')" class="mt-2" />
                            </div>

                        </div> <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                             <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Thông tin Liên hệ (Cập nhật nếu thay đổi)</h3>
                             <div class="mt-4">
                                <x-input-label for="contact_email" value="Email liên hệ (*)" />
                                <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email', $employmentInfo->contact_email ?? '')" required />
                                <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                            </div>
                             <div class="mt-4">
                                <x-input-label for="contact_phone" value="Số điện thoại liên hệ (*)" />
                                <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone', $employmentInfo->contact_phone ?? '')" required />
                                <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                            </div>
                             <div class="mt-4">
                                <x-input-label for="contact_address" value="Địa chỉ liên hệ hiện tại (*)" />
                                 <textarea id="contact_address" name="contact_address" rows="3" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('contact_address', $employmentInfo->contact_address ?? '') }}</textarea>
                                <x-input-error :messages="$errors->get('contact_address')" class="mt-2" />
                            </div>
                        </div>

                         <div class="mt-4">
                            <x-input-label for="notes" value="Ghi chú thêm" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes', $employmentInfo->notes ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>


                        <div class="flex items-center justify-between mt-6">
                             <a href="{{ route('graduate.employment.verify') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Quay lại trang xác thực
                            </a>
                            <x-primary-button>
                                {{ $employmentInfo && $employmentInfo->exists ? 'Cập nhật Khai báo' : 'Gửi Khai báo' }}
                            </x-primary-button>
                        </div>
                    </form>

                     <script>
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
                                // Chỉ yêu cầu teaching_location nếu checkbox is_teaching_related được chọn
                                toggleTeachingLocation();
                            } else {
                                detailsDiv.classList.add('hidden');
                                fieldsToRequire.forEach(field => field ? field.required = false : null);
                                if (isTeachingCheckbox) {
                                    isTeachingCheckbox.checked = false; // Bỏ check nếu không có việc làm
                                    toggleTeachingLocation(); // Ẩn và bỏ yêu cầu địa phương công tác
                                }
                            }
                        }

                         function toggleTeachingLocation() {
                            const isTeachingCheckbox = document.getElementById('is_teaching_related');
                            const teachingLocationDiv = document.getElementById('teaching_location_div');
                            const teachingLocationInput = document.getElementById('teaching_location');

                            if (isTeachingCheckbox && teachingLocationDiv && teachingLocationInput) {
                                if (isTeachingCheckbox.checked) {
                                    teachingLocationDiv.classList.remove('hidden');
                                    teachingLocationInput.required = true; // Bật yêu cầu nhập nếu check
                                } else {
                                    teachingLocationDiv.classList.add('hidden');
                                    teachingLocationInput.required = false; // Tắt yêu cầu nhập
                                }
                            }
                        }

                        // Gọi lần đầu khi load trang để đảm bảo trạng thái đúng
                        document.addEventListener('DOMContentLoaded', function() {
                            toggleEmploymentFields();
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-public-form-layout> {{-- Đóng layout mới --}}