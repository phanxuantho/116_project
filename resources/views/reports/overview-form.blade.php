<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo cáo Tổng quan Sinh viên nhận Hỗ trợ 116') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form id="overview-form" action="{{ route('reports.overview.export') }}" method="GET" target="_blank">
                        <div>
                            <label for="statistic_time" class="block font-medium text-sm text-gray-700">Thời gian thống kê</label>
                            <input id="statistic_time" name="statistic_time" type="text" value="Tính đến ngày {{ date('d/m/Y') }}" required 
                                   class="block mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <button type="button" id="print-button" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                In danh sách
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Xuất Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('overview-form');
            const printButton = document.getElementById('print-button');

            printButton.addEventListener('click', function() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData).toString();
                const printUrl = `{{ route('reports.overview.print') }}?${params}`;
                window.open(printUrl, '_blank');
            });
        });
    </script>
</x-app-layout>