<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cấp phát Sinh hoạt phí (Theo Tháng)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <form action="{{ route('allowances.monthly.preview') }}" method="POST">
                    @csrf
                    
                    {{-- 1. Thông tin Tiền & Thời gian --}}
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-bold mb-4 text-blue-600">Bước 1: Nhập thông tin</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label value="Số tiền / SV (VNĐ)" />
                                <x-text-input type="number" name="amount" class="w-full font-bold text-red-600" value="3630000" required />
                            </div>
                            <div>
                                <x-input-label value="Tháng chi trả" />
                                <select name="payment_month" class="w-full border-gray-300 rounded-md">
                                    @for($i=1; $i<=12; $i++) <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>Tháng {{ $i }}</option> @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Năm dương lịch" />
                                <x-text-input name="payment_year" value="{{ date('Y') }}" class="w-full" required />
                            </div>
                            <div>
                                <x-input-label value="Năm học" />
                                <select name="school_year_id" class="w-full border-gray-300 rounded-md">
                                    @foreach($schoolYears as $y) <option value="{{ $y->id }}">{{ $y->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Học kỳ" />
                                <select name="semester_id" class="w-full border-gray-300 rounded-md">
                                    @foreach($semesters as $s) <option value="{{ $s->id }}">HK{{ $s->semester_number }} ({{ $s->schoolYear->name }})</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Ghi chú" />
                                <x-text-input name="note" class="w-full" />
                            </div>
                        </div>
                    </div>

                    {{-- 2. Chọn Lớp --}}
                    @include('allowances.partials.class_selector')

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>Tiếp tục (Xem Danh sách Nháp)</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>