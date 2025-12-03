<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cấu hình Hệ thống
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if ($errors->any())
                        <div class="mb-4">
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- Lặp qua các nhóm cấu hình --}}
                        @foreach ($settings as $group => $groupSettings)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                    {{-- Hiển thị tên nhóm --}}
                                    @if($group === 'general') Cấu hình Chung 
                                    @elseif($group === 'forms') Cấu hình Form Khai báo
                                    @else {{ ucfirst($group) }} 
                                    @endif
                                </h3>
                                
                                <div class="space-y-4">
                                    {{-- Lặp qua các cấu hình trong nhóm --}}
                                    @foreach ($groupSettings as $setting)
                                        <div>
                                            <x-input-label for="{{ $setting->key }}" value="{{ $setting->label }}" />

                                            {{-- Hiển thị input tương ứng với type --}}
                                            @if ($setting->type === 'text')
                                                <x-text-input id="{{ $setting->key }}" class="block mt-1 w-full" type="text" name="{{ $setting->key }}" :value="old($setting->key, $setting->value)" />
                                            
                                            @elseif ($setting->type === 'textarea')
                                                <textarea id="{{ $setting->key }}" name="{{ $setting->key }}" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old($setting->key, $setting->value) }}</textarea>

                                            @elseif ($setting->type === 'file')
                                                {{-- Hiển thị logo hiện tại nếu có --}}
                                                @if($setting->key === 'app_logo' && $setting->value)
                                                    <img src="{{ Storage::url($setting->value) }}" alt="Logo hiện tại" class="h-16 mt-2 mb-2">
                                                @endif
                                                <input id="{{ $setting->key }}" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="{{ $setting->key }}">
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Tải lên file ảnh mới để thay đổi.</p>
                                            
                                            @elseif ($setting->type === 'toggle')
                                                {{-- Sử dụng checkbox cho toggle --}}
                                                <label class="relative inline-flex items-center cursor-pointer mt-2">
                                                    <input type="checkbox" id="{{ $setting->key }}" name="{{ $setting->key }}" value="1" 
                                                           class="sr-only peer" 
                                                           {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }}>
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{-- (Nhãn phụ nếu cần) --}}</span>
                                                </label>

                                             @elseif ($setting->type === 'number')
                                                <x-text-input id="{{ $setting->key }}" class="block mt-1 w-full" type="number" name="{{ $setting->key }}" :value="old($setting->key, $setting->value)" />

                                            {{-- Thêm các kiểu khác nếu cần --}}
                                            @else 
                                                 <x-text-input id="{{ $setting->key }}" class="block mt-1 w-full" type="text" name="{{ $setting->key }}" :value="old($setting->key, $setting->value)" />
                                            @endif
                                            
                                            <x-input-error :messages="$errors->get($setting->key)" class="mt-2" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button>
                                Lưu Cấu hình
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>