<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu thông tin Tốt nghiệp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Header --}}
    <div class="bg-blue-800 text-white py-6 shadow-md">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-2xl md:text-3xl font-bold uppercase">Cổng Tra cứu Thông tin Tốt nghiệp</h1>
            <p class="text-blue-200 mt-2">Dành cho Địa phương và Đơn vị quản lý</p>
            <p class="text-blue-200 mt-2"><i>Thông tin chi tiết vui lòng liên hệ Chuyên viên Trần Quang Huy - 0948.059.069</i></p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        
        {{-- Form Tìm kiếm --}}
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 max-w-4xl mx-auto relative z-10 border border-gray-200">
            <form action="{{ route('public.graduation.lookup') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label for="province_code" class="block text-sm font-medium text-gray-700 mb-2">Chọn Tỉnh / Thành phố cần tra cứu:</label>
                        <select name="province_code" id="province_code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-10 px-3 border text-gray-700">
                            <option value="">-- Chọn Tỉnh --</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->code }}" {{ request('province_code') == $province->code ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1 italic">* Chỉ hiển thị các tỉnh có sinh viên đã tốt nghiệp.</p>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded h-10 transition duration-150 ease-in-out">
                            🔍 Tra cứu
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Kết quả Tìm kiếm --}}
        <div class="mt-10 max-w-7xl mx-auto"> {{-- Tăng chiều rộng để chứa thêm cột --}}
            @if(request('province_code'))
                @if($students->isNotEmpty())
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">
                            Kết quả tra cứu: <span class="text-blue-600">{{ $selectedProvince->name ?? '' }}</span>
                        </h2>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            Tìm thấy {{ $students->count() }} sinh viên
                        </span>
                    </div>

                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">STT</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Họ tên / Ngày sinh</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Lớp</th> {{-- Cột mới --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Địa chỉ thường trú</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tỉnh</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Loại TN</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Năm TN</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $index => $student)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-500 border-r">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap border-r">
                                            <div class="text-sm font-bold text-gray-900">{{ $student->full_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 border-r font-medium">
                                            {{ $student->class->class_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700 border-r">
                                            {{ $student->old_address_detail }} 
                                            {{ $student->address_detail }} 
                                            @if($student->ward) - {{ $student->ward->name }} @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-700 border-r">
                                            {{ $student->province->name ?? '' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center border-r">
                                            @php 
                                                $rank = $student->graduation->graduation_rank ?? 'N/A';
                                                $color = match($rank) {
                                                    'Xuất sắc' => 'bg-red-100 text-red-800',
                                                    'Giỏi' => 'bg-green-100 text-green-800',
                                                    'Khá' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ $rank }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            {{ $student->graduation->graduation_year ?? '' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded shadow">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Không tìm thấy sinh viên tốt nghiệp nào thuộc tỉnh <b>{{ $selectedProvince->name ?? request('province_code') }}</b>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-10 text-gray-500">
                    <p>Vui lòng chọn Tỉnh/Thành phố và nhấn nút Tra cứu để xem dữ liệu.</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="mt-16 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Hệ thống Quản lý Sinh viên Sư phạm 116.
        </div>
    </div>

</body>
</html>