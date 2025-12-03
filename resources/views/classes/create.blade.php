<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thêm Lớp Mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('classes.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="class_code" :value="__('Mã Lớp')" />
                                <x-text-input id="class_code" class="block mt-1 w-full" type="text" name="class_code" :value="old('class_code')" required autofocus placeholder="VD: 211011" />
                                <x-input-error :messages="$errors->get('class_code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="course_year" :value="__('Khóa (Năm)')" />
                                <x-text-input id="course_year" class="block mt-1 w-full" type="number" name="course_year" :value="old('course_year', date('Y'))" required />
                                <x-input-error :messages="$errors->get('course_year')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="class_name" :value="__('Tên Lớp')" />
                                <x-text-input id="class_name" class="block mt-1 w-full" type="text" name="class_name" :value="old('class_name')" required placeholder="VD: Sư phạm Toán học K21" />
                                <x-input-error :messages="$errors->get('class_name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="major_id" :value="__('Thuộc Ngành')" />
                                <x-select-input name="major_id" id="major_id" class="block mt-1 w-full">
                                    <option value="" disabled selected>-- Chọn Ngành --</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                            {{ $major->major_name }} ({{ $major->faculty->faculty_name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('major_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="class_status" :value="__('Trạng thái')" />
                                <x-select-input name="class_status" id="class_status" class="block mt-1 w-full">
                                    <option value="Đang học" {{ old('class_status') === 'Đang học' ? 'selected' : '' }}>Đang học</option>
                                    <option value="Đã tốt nghiệp" {{ old('class_status') === 'Đã tốt nghiệp' ? 'selected' : '' }}>Đã tốt nghiệp</option>
                                    <option value="Đã huỷ" {{ old('class_status') === 'Đã huỷ' ? 'selected' : '' }}>Đã huỷ</option>
                                </x-select-input>
                                <x-input-error :messages="$errors->get('class_status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="class_size" :value="__('Sĩ số (Dự kiến)')" />
                                <x-text-input id="class_size" class="block mt-1 w-full" type="number" name="class_size" :value="old('class_size', 0)" min="0" />
                                <x-input-error :messages="$errors->get('class_size')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('classes.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline rounded-md mr-4">
                                {{ __('Hủy bỏ') }}
                            </a>
                            <x-primary-button>
                                {{ __('Lưu Lớp Mới') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>