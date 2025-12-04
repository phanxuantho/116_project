<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Báo cáo Danh sách Sinh viên theo Tỉnh (TK01)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    
                    {{-- Header Section --}}
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                        <h3 class="text-lg font-medium leading-6">Bộ lọc Báo cáo</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Chọn điều kiện lọc để xuất danh sách.</p>
                    </div>

                    {{-- Form gửi GET request --}}
                    <form action="{{ route('reports.province_students.print') }}" method="GET" target="_blank" id="report-form">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="course_year" :value="__('Chọn Khóa (Năm tuyển sinh) (*)')" />
                                <x-select-input name="course_year" id="course_year" class="block mt-1 w-full" required>
                                    <option value="" disabled selected>-- Chọn năm --</option>
                                    @foreach($courseYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>

                            <div>
                                <x-input-label for="province_code" :value="__('Chọn Tỉnh/Thành phố')" />
                                <x-select-input name="province_code" id="province_code" class="block mt-1 w-full">
                                    <option value="">-- Tất cả các tỉnh (In hàng loạt) --</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->code }}">{{ $province->name }}</option>
                                    @endforeach
                                </x-select-input>
                                <p class="text-xs text-gray-500 mt-2">Để trống ô này để in danh sách của tất cả các tỉnh (tự động ngắt trang).</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 space-x-3">
                            {{-- Nút Xuất Excel --}}
                            <button type="submit" 
                                    formaction="{{ route('reports.province_students.export') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                {{ __('Xuất Excel') }}
                            </button>

                            {{-- Nút Xem & In --}}
                            <x-primary-button>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                {{ __('Xem & In Báo Cáo') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>