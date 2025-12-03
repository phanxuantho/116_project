<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chỉnh sửa Ngành: ') . $major->major_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('majors.update', $major->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="major_code" :value="__('Mã Ngành')" />
                                <x-text-input id="major_code" class="block mt-1 w-full" type="text" name="major_code" :value="old('major_code', $major->major_code)" required />
                                <x-input-error :messages="$errors->get('major_code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="major_name" :value="__('Tên Ngành')" />
                                <x-text-input id="major_name" class="block mt-1 w-full" type="text" name="major_name" :value="old('major_name', $major->major_name)" required />
                                <x-input-error :messages="$errors->get('major_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="faculty_id" :value="__('Khoa Trực Thuộc')" />
                                <x-select-input name="faculty_id" id="faculty_id" class="block mt-1 w-full">
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('faculty_id', $major->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->faculty_name }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('majors.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline mr-4">
                                {{ __('Hủy bỏ') }}
                            </a>
                            <x-primary-button>
                                {{ __('Cập nhật') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>