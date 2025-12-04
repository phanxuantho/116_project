<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Báo cáo Kết quả học tập (TK02)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('reports.province_results.print') }}" method="GET" target="_blank">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div>
                            <x-input-label for="school_year_id" :value="__('Năm học (*)')" />
                            <x-select-input name="school_year_id" id="school_year_id" class="block mt-1 w-full" required>
                                <option value="">-- Chọn Năm học --</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="semester" :value="__('Học kỳ (*)')" />
                            <x-select-input name="semester" id="semester" class="block mt-1 w-full" required>
                                <option value="">-- Chọn Học kỳ --</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem }}">Học kỳ {{ $sem }}</option>
                                @endforeach
                            </x-select-input>
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
                                formaction="{{ route('reports.province_results.export') }}" 
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