<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tổng quan Hệ thống (Dashboard)') }}
        </h2>
    </x-slot>

    {{-- Nhúng thư viện Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. Khối Cards: Thống kê số lượng tổng quan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Tổng sinh viên quản lý</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalStudents) }}</div>
                    <div class="text-xs text-gray-400 mt-1">Toàn bộ hồ sơ trong hệ thống</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Sinh viên Đang học</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($activeStudents) }}</div>
                    <div class="text-xs text-gray-400 mt-1">Đủ điều kiện xét duyệt cơ bản</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Tổng ngân sách đã giải ngân</div>
                    <div class="mt-2 text-3xl font-bold text-purple-700">{{ number_format(array_sum($budgetData)) }} đ</div>
                    <div class="text-xs text-gray-400 mt-1">Tính trên các kỳ học hiển thị</div>
                </div>
            </div>

            {{-- 2. Khối Biểu đồ hàng 1: Trạng thái & Funding --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tỷ lệ Trạng thái Sinh viên</h3>
                    <div class="relative h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Trạng thái Nhận kinh phí</h3>
                    <div class="relative h-64">
                        <canvas id="fundingChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 3. Khối Biểu đồ hàng 2: Kết quả học tập & Ngân sách --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 col-span-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Kết quả học tập (Kỳ gần nhất)</h3>
                    <div class="relative h-64">
                        <canvas id="academicChart"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6 md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Biểu đồ Giải ngân Ngân sách theo Kỳ</h3>
                    <div class="relative h-64">
                        <canvas id="budgetChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Banner chào mừng cũ (Giữ lại ở cuối hoặc bỏ nếu muốn) --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg text-white p-6 shadow-lg flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold">Hệ thống Quản lý Sinh viên Sư phạm (NĐ 116)</h3>
                    <p class="text-blue-100 text-sm">Phiên bản 1.0 - Phát triển bởi Phòng Công tác Sinh viên</p>
                </div>
                <a href="{{ route('students.index') }}" class="bg-white text-blue-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-gray-100 shadow">
                    Vào danh sách SV &rarr;
                </a>
            </div>

        </div>
    </div>

    {{-- SCRIPT VẼ BIỂU ĐỒ --}}
    <script>
        // 1. Config Biểu đồ Trạng thái (Pie)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($statusStats)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($statusStats)) !!},
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#6B7280'], // Màu: Xanh, Vàng, Đỏ, Lam, Xám
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // 2. Config Biểu đồ Funding (Doughnut)
        const fundingCtx = document.getElementById('fundingChart').getContext('2d');
        new Chart(fundingCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($fundingStats)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($fundingStats)) !!},
                    backgroundColor: ['#34D399', '#FCD34D', '#F87171', '#60A5FA'],
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // 3. Config Biểu đồ Học tập (Bar - Vertical)
        const academicCtx = document.getElementById('academicChart').getContext('2d');
        new Chart(academicCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($academicStats)) !!},
                datasets: [{
                    label: 'Số lượng SV',
                    data: {!! json_encode(array_values($academicStats)) !!},
                    backgroundColor: '#6366F1', // Màu Indigo
                    borderRadius: 5
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        // 4. Config Biểu đồ Ngân sách (Line + Area)
        const budgetCtx = document.getElementById('budgetChart').getContext('2d');
        new Chart(budgetCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($budgetLabels) !!},
                datasets: [{
                    label: 'Số tiền giải ngân (VNĐ)',
                    data: {!! json_encode($budgetData) !!},
                    borderColor: '#8B5CF6', // Purple
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    fill: true,
                    tension: 0.3 // Đường cong mềm mại
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>