<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cấp phát Sinh hoạt phí (Theo Tháng)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- VÙNG HIỂN THỊ THÔNG BÁO --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Thành công!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Lỗi!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Vui lòng kiểm tra lại dữ liệu:</p>
                    <ul class="list-disc pl-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="bg-white p-6 rounded-lg shadow">
                <form action="{{ route('allowances.monthly.preview') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-bold mb-4 text-blue-600">Bước 1: Nhập thông tin</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Số tiền --}}
                            <div>
                                <x-input-label value="Số tiền / SV (VNĐ)" />
                                <x-text-input type="number" name="amount" class="block mt-1 w-full font-bold text-red-600" value="3630000" required />
                            </div>
                            
                            {{-- Trạng thái chi trả --}}
                            <div>
                                <x-input-label value="Trạng thái chi trả" />
                                <x-select-input name="status" class="block mt-1 w-full">
                                    <option value="Đã chi trả" selected>Đã chi trả</option>
                                    <option value="Chưa chi trả">Chưa chi trả</option>
                                </x-select-input>
                            </div>

                            {{-- Tháng chi trả --}}
                            <div>
                                <x-input-label value="Tháng chi trả" />
                                <x-select-input name="payment_month" class="block mt-1 w-full">
                                    @for($i=1; $i<=12; $i++) 
                                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>Tháng {{ $i }}</option> 
                                    @endfor
                                </x-select-input>
                            </div>

                            {{-- Năm dương lịch --}}
                            <div>
                                <x-input-label value="Năm dương lịch" />
                                <x-text-input name="payment_year" value="{{ date('Y') }}" class="block mt-1 w-full" required />
                            </div>

                            {{-- Năm học --}}
                            <div>
                                <x-input-label value="Thuộc Năm học" />
                                <x-select-input name="school_year_id" class="block mt-1 w-full">
                                    @foreach($schoolYears as $y) 
                                        <option value="{{ $y->id }}">{{ $y->name }}</option> 
                                    @endforeach
                                </x-select-input>
                            </div>

                            {{-- Học kỳ --}}
                            <div>
                                <x-input-label value="Thuộc Học kỳ" />
                                <x-select-input name="semester_id" class="block mt-1 w-full">
                                    @foreach($semesters as $s) 
                                        <option value="{{ $s->id }}">HK{{ $s->semester_number }} ({{ $s->schoolYear->name }})</option> 
                                    @endforeach
                                </x-select-input>
                            </div>

                            {{-- Ghi chú --}}
                            <div class="md:col-span-3">
                                <x-input-label value="Ghi chú (Optional)" />
                                <x-text-input name="note" class="block mt-1 w-full" />
                            </div>
                        </div>
                    </div>

                    @include('allowances.partials.class_selector')

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>Tiếp tục (Xem Danh sách Nháp)</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>