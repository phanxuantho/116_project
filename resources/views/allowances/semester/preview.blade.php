<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Duyệt Danh sách Cấp phát Theo Kỳ (Nháp)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                
                {{-- Thông báo Draft --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 flex items-start">
                    <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-yellow-700">CHẾ ĐỘ XEM TRƯỚC (DRAFT)</p>
                        <p class="text-sm text-yellow-600">
                            Dữ liệu dưới đây chưa được lưu vào hệ thống. Vui lòng kiểm tra kỹ số liệu trước khi bấm nút "Phê duyệt".<br>
                            Tổng số sinh viên: <b>{{ $students->count() }}</b> (từ <b>{{ $classes_count }}</b> lớp đã chọn).
                        </p>
                    </div>
                </div>

                {{-- Thông tin chung --}}
                <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm bg-gray-50 p-4 rounded border">
                    <div>
                        <span class="text-gray-500">Mức tiền/SV:</span><br>
                        <span class="font-bold text-blue-600">{{ number_format($input['amount']) }} đ</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Đợt số:</span><br>
                        <span class="font-bold">{{ $input['installment_number'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Số tháng hưởng:</span><br>
                        <span class="font-bold">{{ $input['months_covered'] }} tháng</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ghi chú:</span><br>
                        <span class="italic">{{ $input['note'] ?? 'Không có' }}</span>
                    </div>
                </div>

                {{-- Bảng danh sách chi tiết --}}
                <div class="overflow-x-auto mb-6 border rounded-lg max-h-[500px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MSSV</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Sửa: Bỏ $index =>, chỉ cần $st --}}
                            @forelse($students as $st)
                            <tr class="hover:bg-gray-50">
                                {{-- SỬA LỖI: Dùng $loop->iteration thay vì $index + 1 --}}
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $st->student_code }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $st->full_name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $st->class->class_name }}</td>
                                <td class="px-4 py-2 text-xs">
                                    <span class="px-2 py-1 rounded-full {{ $st->status == 'Đang học' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $st->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right text-sm font-bold text-gray-800">
                                    {{ number_format($input['amount']) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                    Không tìm thấy sinh viên nào phù hợp với điều kiện lọc.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($students->isNotEmpty())
                        <tfoot class="bg-gray-50 sticky bottom-0 z-10 font-bold">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right text-gray-700">TỔNG CỘNG:</td>
                                <td class="px-4 py-3 text-right text-red-600 text-lg">
                                    {{ number_format($students->count() * $input['amount']) }} VNĐ
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Form Hành động --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('allowances.semester.create') }}" class="text-gray-600 hover:text-gray-900 underline">
                        &laquo; Quay lại chỉnh sửa
                    </a>

                    @if($students->isNotEmpty())
                        <form action="{{ route('allowances.semester.store') }}" method="POST">
                            @csrf
                            
                            {{-- TRUYỀN DỮ LIỆU SANG BƯỚC LƯU --}}
                            <input type="hidden" name="meta" value="{{ json_encode($input) }}">
                            
                            {{-- Chỉ cần truyền danh sách MSSV để tiết kiệm băng thông --}}
                            <input type="hidden" name="data" value="{{ json_encode($students->map(fn($s) => ['student_code' => $s->student_code])) }}">

                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none transition ease-in-out duration-150 shadow-md" onclick="return confirm('Hệ thống sẽ tạo {{ $students->count() }} bản ghi kinh phí. Bạn có chắc chắn không?')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                PHÊ DUYỆT & LƯU
                            </button>
                        </form>
                    @else
                        <button disabled class="px-6 py-3 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed font-semibold uppercase">
                            Không có dữ liệu để lưu
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>