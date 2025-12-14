<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Duyệt Danh sách Cấp phát (Nháp)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                
                {{-- Thông báo tổng quan --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Bạn đang xem bản <b>NHÁP</b>. Dữ liệu chưa được lưu vào hệ thống.<br>
                                Tổng số sinh viên tìm thấy: <b>{{ $students->count() }}</b> trên tổng số <b>{{ $classes_count }}</b> lớp đã chọn.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bảng danh sách chi tiết --}}
                <div class="overflow-x-auto mb-6 max-h-96 overflow-y-auto border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left">STT</th>
                                <th class="px-4 py-2 text-left">MSSV</th>
                                <th class="px-4 py-2 text-left">Họ tên</th>
                                <th class="px-4 py-2 text-left">Lớp</th>
                                <th class="px-4 py-2 text-left">Trạng thái</th>
                                <th class="px-4 py-2 text-right">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $st)
                            <tr>
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">{{ $st->student_code }}</td>
                                <td class="px-4 py-2">{{ $st->full_name }}</td>
                                <td class="px-4 py-2">{{ $st->class->class_name }}</td>
                                <td class="px-4 py-2 text-xs">
                                    {{ $st->status }} / {{ $st->funding_status }}
                                </td>
                                <td class="px-4 py-2 text-right font-bold text-gray-700">
                                    {{ number_format($input['amount']) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tổng tiền --}}
                <div class="flex justify-end items-center mb-6 text-xl">
                    <span class="mr-4">Tổng tiền dự kiến:</span>
                    <span class="font-bold text-red-600">{{ number_format($students->count() * $input['amount']) }} VNĐ</span>
                </div>

                {{-- Form Xác nhận (Gửi dữ liệu thật đi lưu) --}}
                <form action="{{ route('allowances.monthly.store') }}" method="POST" class="flex justify-between border-t pt-4">
                    @csrf
                    
                    {{-- Truyền dữ liệu dạng JSON ẩn --}}
                    <input type="hidden" name="meta" value="{{ json_encode($input) }}">
                    <input type="hidden" name="data" value="{{ json_encode($students->map(fn($s) => ['student_code' => $s->student_code])) }}">

                    <a href="{{ route('allowances.monthly.create') }}" class="text-gray-600 underline self-center">Quay lại sửa</a>
                    
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md font-bold hover:bg-green-700 shadow-lg" onclick="return confirm('Bạn có chắc chắn muốn duyệt và lưu danh sách này?')">
                        ✓ PHÊ DUYỆT & LƯU DANH SÁCH
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>