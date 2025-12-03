<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thêm Khoa Mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('faculties.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="faculty_code" :value="__('Mã Khoa')" />
                                <x-text-input id="faculty_code" class="block mt-1 w-full" type="text" name="faculty_code" :value="old('faculty_code')" required autofocus placeholder="VD: CNTT" />
                                <x-input-error :messages="$errors->get('faculty_code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="faculty_name" :value="__('Tên Khoa')" />
                                <x-text-input id="faculty_name" class="block mt-1 w-full" type="text" name="faculty_name" :value="old('faculty_name')" required placeholder="VD: Khoa Công nghệ Thông tin" />
                                <x-input-error :messages="$errors->get('faculty_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('faculties.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline rounded-md mr-4">
                                {{ __('Hủy bỏ') }}
                            </a>
                            <x-primary-button>
                                {{ __('Lưu Khoa Mới') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>