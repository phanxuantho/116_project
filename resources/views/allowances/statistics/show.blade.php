<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chi tiết Danh sách Cấp phát') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                
                <div class="mb-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-blue-700 uppercase">{{ $title }}</h3>
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                        🖨️ In Danh sách
                    </button>
                </div>

                <div class="overflow-x-auto border rounded">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-center w-12 text-xs font-bold text-gray-500 uppercase">STT</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">MSSV</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Họ và tên</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Trạng thái</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Số tiền</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-center text-sm">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->student_code }}</td>
                                <td class="px-4 py-2 text-sm font-medium">{{ $item->student->full_name }}</td>
                                <td class="px-4 py-2 text-xs">
                                    <span class="px-2 py-1 rounded {{ $item->status == 'Đã chi trả' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right text-sm font-bold">{{ number_format($item->amount) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500 italic">{{ $item->note }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">Tổng cộng:</td>
                                <td class="px-4 py-3 text-right text-blue-600">{{ number_format($students->sum('amount')) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <a href="javascript:history.back()" class="text-indigo-600 hover:underline text-sm">&laquo; Quay lại trang thống kê</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>