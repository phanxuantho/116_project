<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lập Danh sách Cấp phát theo Đợt (Kỳ)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- VÙNG HIỂN THỊ THÔNG BÁO --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Thành công!</strong> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Lỗi!</strong> {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif
            {{-- --------------------------------------- --}}

            <div class="bg-white p-6 rounded-lg shadow">
                <form action="{{ route('allowances.semester.preview') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-bold mb-4 text-blue-600 uppercase">1. Thông tin Cấp phát (Theo Kỳ)</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Số tiền --}}
                            <div>
                                <x-input-label for="amount" :value="__('Số tiền Tổng / SV (VNĐ)')" />
                                <x-text-input id="amount" class="block mt-1 w-full font-bold text-red-600" 
                                              type="number" name="amount" :value="old('amount')" required />
                            </div>

                            {{-- Trạng thái chi trả --}}
                            <div>
                                <x-input-label value="Trạng thái chi trả" />
                                <x-select-input name="status" class="block mt-1 w-full">
                                    <option value="Đã chi trả" selected>Đã chi trả</option>
                                    <option value="Chưa chi trả">Chưa chi trả</option>
                                </x-select-input>
                            </div>

                            {{-- Chọn Học kỳ --}}
                            <div>
                                <x-input-label for="semester_id" :value="__('Thuộc Học kỳ')" />
                                <x-select-input name="semester_id" id="semester_id" class="block mt-1 w-full" required>
                                    @foreach($semesters as $s)
                                        <option value="{{ $s->id }}">HK{{ $s->semester_number }} ({{ $s->schoolYear->name }})</option>
                                    @endforeach
                                </x-select-input>
                            </div>

                            {{-- Đợt số --}}
                            <div>
                                <x-input-label for="installment_number" :value="__('Đợt cấp thứ (Installment)')" />
                                <x-text-input id="installment_number" class="block mt-1 w-full" 
                                              type="number" name="installment_number" :value="old('installment_number', 1)" required />
                            </div>

                            {{-- Số tháng hưởng --}}
                            <div>
                                <x-input-label for="months_covered" :value="__('Số tháng được hưởng')" />
                                <x-text-input id="months_covered" class="block mt-1 w-full" 
                                              type="number" step="0.1" name="months_covered" :value="old('months_covered', 5)" required />
                            </div>

                            {{-- Bắt đầu từ tháng --}}
                            <div>
                                <x-input-label for="start_month" :value="__('Bắt đầu từ tháng (Start Month)')" />
                                <x-text-input id="start_month" class="block mt-1 w-full" 
                                              type="number" name="start_month" :value="old('start_month')" placeholder="VD: 1 hoặc 8" />
                            </div>

                            {{-- Ghi chú --}}
                            <div class="md:col-span-3">
                                <x-input-label for="note" :value="__('Ghi chú (Optional)')" />
                                <x-text-input id="note" class="block mt-1 w-full" type="text" name="note" :value="old('note')" />
                            </div>
                        </div>
                    </div>

                    @include('allowances.partials.class_selector')

                    <div class="mt-8 flex justify-end pt-4 border-t border-gray-200">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            {{ __('Tiếp tục (Xem Danh sách Nháp)') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>