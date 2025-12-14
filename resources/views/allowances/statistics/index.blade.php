<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Thống kê Quá trình Cấp phát') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                
                {{-- BỘ LỌC --}}
                <form method="GET" action="{{ route('allowances.statistics.index') }}" class="mb-8 border-b pb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label value="Năm học" />
                            <x-select-input name="school_year_id" class="w-full" onchange="this.form.submit()">
                                <option value="">-- Tất cả --</option>
                                @foreach($schoolYears as $y)
                                    <option value="{{ $y->id }}" {{ request('school_year_id') == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label value="Học kỳ" />
                            <x-select-input name="semester_id" class="w-full" onchange="this.form.submit()">
                                <option value="">-- Tất cả --</option>
                                @foreach($semesters as $s)
                                    <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>HK{{ $s->semester_number }} ({{ $s->schoolYear->name }})</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label value="Khoa" />
                            <x-select-input name="faculty_id" class="w-full" onchange="this.form.submit()">
                                <option value="">-- Tất cả --</option>
                                @foreach($faculties as $f)
                                    <option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label value="Lớp" />
                            <x-select-input name="class_id" class="w-full">
                                <option value="">-- Tất cả --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->class_name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <x-primary-button>Lọc dữ liệu</x-primary-button>
                    </div>
                </form>

                {{-- BẢNG KẾT QUẢ --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">STT</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khoa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lớp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đợt cấp phát</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $totalMoney = 0; @endphp
                            @forelse($statistics as $index => $item)
                                @php 
                                    $totalMoney += $item->total_amount;
                                    // Tạo link chi tiết dựa trên loại
                                    $detailUrl = '#';
                                    $batchName = '';
                                    
                                    if ($item->type == 'monthly') {
                                        $batchName = "Sinh hoạt phí Tháng {$item->payment_month}/{$item->payment_year}";
                                        $detailUrl = route('allowances.statistics.show', [
                                            'type' => 'monthly',
                                            'class_id' => $item->class_id,
                                            'month' => $item->payment_month,
                                            'year' => $item->payment_year
                                        ]);
                                    } else {
                                        $batchName = "Học kỳ {$item->semester_number} ({$item->school_year_name}) - Đợt {$item->installment_number}";
                                        $detailUrl = route('allowances.statistics.show', [
                                            'type' => 'semester',
                                            'class_id' => $item->class_id,
                                            'semester_id' => $item->semester_id,
                                            'installment' => $item->installment_number
                                        ]);
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $item->faculty_name }}</td>
                                    <td class="px-4 py-3 text-sm font-medium">{{ $item->class_name }}</td>
                                    <td class="px-4 py-3 text-sm text-blue-600">{{ $batchName }}</td>
                                    <td class="px-4 py-3 text-center">{{ $item->total_students }} SV</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-700">{{ number_format($item->total_amount) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ $detailUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold underline">
                                            Xem DS
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">Không có dữ liệu cấp phát nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($statistics->isNotEmpty())
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right">TỔNG CỘNG TOÀN BỘ:</td>
                                    <td class="px-4 py-3 text-right text-red-600 text-lg">{{ number_format($totalMoney) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>