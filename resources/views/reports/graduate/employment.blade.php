<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Thống kê Việc làm Sinh viên Tốt nghiệp
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. BỘ LỌC DỮ LIỆU --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="GET" action="{{ route('reports.graduate.employment') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    
                    {{-- Chọn Khóa --}}
                    <div>
                        <x-input-label value="Khóa học" />
                        <select name="course_year" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">-- Tất cả các khóa --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course }}" {{ request('course_year') == $course ? 'selected' : '' }}>
                                    Khóa {{ $course }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Chọn Lớp --}}
                    <div>
                        <x-input-label value="Lớp" />
                        <select name="class_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Tất cả các lớp --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Chọn Tình trạng --}}
                    <div>
                        <x-input-label value="Tình trạng việc làm" />
                        <select name="status" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Đã có việc làm" {{ request('status', 'Đã có việc làm') == 'Đã có việc làm' ? 'selected' : '' }}>Đã có việc làm</option>
                            <option value="Chưa có việc làm" {{ request('status') == 'Chưa có việc làm' ? 'selected' : '' }}>Chưa có việc làm</option>
                            <option value="Đang học nâng cao" {{ request('status') == 'Đang học nâng cao' ? 'selected' : '' }}>Đang học nâng cao</option>
                            <option value="Chưa khai báo" {{ request('status') == 'Chưa khai báo' ? 'selected' : '' }}>Chưa khai báo (Missing)</option>
                            <option value="Tất cả" {{ request('status') == 'Tất cả' ? 'selected' : '' }}>-- Tất cả --</option>
                        </select>
                    </div>

                    {{-- Nút Lọc & Export --}}
                    <div class="flex gap-2">
                        <x-primary-button type="submit" class="h-10">Lọc dữ liệu</x-primary-button>
                        
                        {{-- Nút Export Excel: Copy query parameters hiện tại sang link export --}}
                        <a href="{{ route('reports.graduate.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 h-10">
                            Xuất Excel
                        </a>
                        
                         <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 h-10">
                            In
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. BIỂU ĐỒ THỐNG KÊ (Tổng quan) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow flex flex-col justify-center items-center">
                    <span class="text-gray-500 text-sm font-medium uppercase">Tổng Sinh viên Tốt nghiệp</span>
                    <span class="text-4xl font-bold text-indigo-600 my-2">{{ $totalGraduates }}</span>
                    <span class="text-xs text-gray-400 text-center">(Theo bộ lọc Khóa/Lớp hiện tại)</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow col-span-2 relative h-64">
                    <h3 class="text-sm font-bold text-gray-600 absolute top-4 left-4">Tỷ lệ Tình trạng Việc làm</h3>
                    <div class="flex justify-center h-full">
                         <canvas id="employmentChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 3. DANH SÁCH CHI TIẾT --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-center w-12 font-medium text-gray-500 uppercase">STT</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase">Sinh viên</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-500 uppercase">Tình trạng</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase">Nơi làm việc</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase">Vị trí / Ngành</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase">Liên hệ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($students as $st)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-4 text-center">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                                    <td class="px-3 py-4">
                                        <div class="font-bold text-gray-900">{{ $st->full_name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $st->student_code }}</div>
                                        <div class="text-blue-500 text-xs">{{ $st->class->class_name ?? '' }}</div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        @if($st->employment)
                                            @php
                                                $color = match($st->employment->employment_status) {
                                                    'Đã có việc làm' => 'bg-green-100 text-green-800',
                                                    'Chưa có việc làm' => 'bg-yellow-100 text-yellow-800',
                                                    'Đang học nâng cao' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                                {{ $st->employment->employment_status }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                Chưa khai báo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-gray-600">
                                        @if($st->employment)
                                            <div class="font-medium">{{ $st->employment->company_name }}</div>
                                            <div class="text-xs italic">{{ $st->employment->teachingProvince->name ?? '' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">
                                        @if($st->employment)
                                            <div>{{ $st->employment->job_title }}</div>
                                            <div class="text-xs text-gray-500">{{ $st->employment->employment_type }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-gray-500">
                                        @if($st->employment)
                                            @if($st->employment->contact_phone) <div>📞 {{ $st->employment->contact_phone }}</div> @endif
                                            @if($st->employment->contact_email) <div class="text-xs">✉️ {{ $st->employment->contact_email }}</div> @endif
                                        @else
                                            {{ $st->phone }} <span class="text-xs">(SĐT SV)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Không tìm thấy dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Phân trang --}}
                <div class="p-4">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Script vẽ biểu đồ --}}
    <script>
        const ctx = document.getElementById('employmentChart').getContext('2d');
        const data = {!! json_encode($chartData) !!};
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: [
                        '#10B981', // Đã có việc làm (Green)
                        '#F59E0B', // Chưa có việc làm (Yellow)
                        '#3B82F6', // Đang học nâng cao (Blue)
                        '#EF4444', // Chưa khai báo (Red)
                        '#6B7280'  // Khác (Gray)
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    </script>
</x-app-layout>