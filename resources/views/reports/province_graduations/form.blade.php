<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Báo cáo Tốt nghiệp (TK03)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('reports.province_graduations.print') }}" method="GET" target="_blank">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div>
                            <x-input-label for="course_year" :value="__('Khóa (Năm tuyển sinh) (*)')" />
                            <x-select-input name="course_year" id="course_year" class="block mt-1 w-full" required>
                                <option value="">-- Chọn năm --</option>
                                @foreach($courseYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="decision_number" :value="__('Số Quyết định (Đợt TN)')" />
                            <x-select-input name="decision_number" id="decision_number" class="block mt-1 w-full">
                                <option value="">-- Tất cả các đợt --</option>
                                @foreach($decisionNumbers as $num)
                                    <option value="{{ $num }}">{{ $num }}</option>
                                @endforeach
                            </x-select-input>
                            <p class="text-xs text-gray-500 mt-1">Chọn số quyết định để in theo đợt.</p>
                        </div>

                        <div>
                            <x-input-label for="province_code" :value="__('Tỉnh/Thành phố')" />
                            <x-select-input name="province_code" id="province_code" class="block mt-1 w-full">
                                <option value="">-- Tất cả (In hàng loạt) --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->code }}">{{ $province->name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-3 pt-4 border-t">
                        <button type="submit" 
                                formaction="{{ route('reports.province_graduations.export') }}" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none transition ease-in-out duration-150">
                            Xuất Excel
                        </button>

                        <x-primary-button>
                            Xem & In Báo Cáo
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>