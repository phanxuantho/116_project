<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Đồng bộ dữ liệu hệ thống đào tạo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Đồng bộ dữ liệu (TTN API)</h1>
                <div class="text-right">
                    <span class="block text-sm text-gray-500">Server Delphi</span>
                    <span class="block text-xs font-mono text-green-600">http://203.162.230.229:8080</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Cột trái: Bộ lọc & Hành động --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Card: Cấu hình tham số --}}
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-3 pb-2 border-b text-sm uppercase">🛠️ Tham số cấu hình</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mã Đơn Vị / Khoa (maDV)</label>
                                <input type="text" id="ma_dv" class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 text-sm" value="0800">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Năm Học</label>
                                    <input type="text" id="nam_hoc" class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 text-sm" value="2025">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Học Kỳ</label>
                                    <input type="text" id="hoc_ky" class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 text-sm" value="1">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mã Lớp (MaLop)</label>
                                <input type="text" id="ma_lop" class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 text-sm" value="251011">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mã Sinh Viên (MaSV)</label>
                                <input type="text" id="ma_sv" class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 text-sm" value="25101005">
                            </div>
                        </div>
                    </div>

                    {{-- Card: Chức năng --}}
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-200 overflow-y-auto max-h-[600px]">
                        <h3 class="font-bold text-gray-700 mb-3 pb-2 border-b text-sm uppercase">🚀 Thao tác</h3>
                        
                        <div class="mb-4">
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase">-- Đơn vị & Cán bộ --</p>
                            <div class="space-y-2">
                                <button onclick="fetchData('units')" class="w-full px-3 py-2 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 text-left text-sm font-semibold border border-indigo-200 flex justify-between items-center">
                                    <span>🏢 Thông Tin Đơn Vị</span>
                                </button>
                                <button onclick="fetchData('lecturers')" class="w-full px-3 py-2 bg-pink-50 text-pink-700 rounded hover:bg-pink-100 text-left text-sm font-semibold border border-pink-200 flex justify-between items-center">
                                    <span>👨‍🏫 Danh Sách CBVC</span>
                                </button>
                                <button onclick="fetchData('gio_gdkh')" class="w-full px-3 py-2 bg-purple-50 text-purple-700 rounded hover:bg-purple-100 text-left text-sm font-semibold border border-purple-200 flex justify-between items-center">
                                    <span>⏳ Giờ GDKH</span>
                                </button>
                                <button onclick="fetchData('lop_khoa')" class="w-full px-3 py-2 bg-blue-50 text-blue-700 rounded hover:bg-blue-100 text-left text-sm font-semibold border border-blue-200 flex justify-between items-center">
                                    <span>🏫 Danh Sách Lớp Khoa</span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase">-- Lớp học --</p>
                            <div class="space-y-2">
                                <button onclick="fetchData('sv_lop')" class="w-full px-3 py-2 bg-teal-50 text-teal-700 rounded hover:bg-teal-100 text-left text-sm font-semibold border border-teal-200 flex justify-between items-center">
                                    <span>👥 Danh Sách SV Lớp</span>
                                </button>
                                <button onclick="fetchData('kehoach_lop')" class="w-full px-3 py-2 bg-orange-50 text-orange-700 rounded hover:bg-orange-100 text-left text-sm font-semibold border border-orange-200 flex justify-between items-center">
                                    <span>📅 Kế Hoạch Lớp</span>
                                </button>
                                <button onclick="fetchData('bangdiem_lop')" class="w-full px-3 py-2 bg-red-50 text-red-700 rounded hover:bg-red-100 text-left text-sm font-semibold border border-red-200 flex justify-between items-center">
                                    <span>📊 Bảng Điểm Lớp</span>
                                </button>
                                <button onclick="fetchData('kqht_lop')" class="w-full px-3 py-2 bg-yellow-50 text-yellow-700 rounded hover:bg-yellow-100 text-left text-sm font-semibold border border-yellow-200 flex justify-between items-center">
                                    <span>🎓 Kết Quả Học Tập</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase">-- Sinh Viên --</p>
                            <button onclick="fetchData('sv_info')" class="w-full px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-left text-sm font-semibold border border-gray-300 flex justify-between items-center">
                                <span>🔍 Tra Cứu Sinh Viên</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Cột phải: Kết quả JSON --}}
                <div class="lg:col-span-3">
                    <div class="bg-white shadow rounded-lg border border-gray-200 h-full min-h-[600px] flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                            <h3 class="font-medium text-gray-700">Kết quả phản hồi JSON</h3>
                            
                            {{-- Nút Import --}}
                            <button id="btn-import" onclick="importToDB()" class="hidden items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                                Import vào CSDL
                            </button>
                        </div>
                        
                        <div id="status-msg" class="hidden px-4 py-2 text-sm"></div>
                        
                        <div class="flex-1 p-4 overflow-auto bg-slate-900 text-green-400 font-mono text-xs relative rounded-b-lg">
                            <div id="loading" class="hidden absolute inset-0 flex items-center justify-center bg-slate-900/80 z-10">
                                <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-green-500"></div>
                            </div>
                            <pre id="json-viewer">Vui lòng cấu hình tham số và chọn chức năng...</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentData = null;
        let currentType = null;

        async function fetchData(type) {
            const viewer = document.getElementById('json-viewer');
            const loading = document.getElementById('loading');
            const btnImport = document.getElementById('btn-import');
            const statusMsg = document.getElementById('status-msg');
            
            const payload = {
                type: type,
                ma_dv: document.getElementById('ma_dv').value,
                nam_hoc: document.getElementById('nam_hoc').value,
                hoc_ky: document.getElementById('hoc_ky').value,
                ma_lop: document.getElementById('ma_lop').value,
                ma_sv: document.getElementById('ma_sv').value
            };

            loading.classList.remove('hidden');
            btnImport.classList.add('hidden');
            statusMsg.classList.add('hidden');
            viewer.textContent = 'Đang kết nối đến API...';
            
            try {
                const response = await fetch('{{ route("sync.fetch") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload) 
                });

                const result = await response.json();

                if (result.success) {
                    viewer.textContent = JSON.stringify(result.data, null, 4);
                    currentData = result.data;
                    currentType = type;
                    
                    let countInfo = Array.isArray(result.data) ? `(${result.data.length} bản ghi)` : '';
                    showStatus(`✅ ${result.message} ${countInfo}`, 'success');

                    // Hiện nút Import nếu là các loại dữ liệu đã hỗ trợ Import (Hiện tại chỉ mới hỗ trợ Khoa, Lớp, SV)
                    if (['units', 'lop_khoa', 'sv_lop'].includes(type) && Array.isArray(result.data) && result.data.length > 0) {
                        btnImport.classList.remove('hidden');
                        btnImport.classList.add('flex');
                    }
                } else {
                    viewer.textContent = JSON.stringify(result, null, 4);
                    showStatus('❌ ' + (result.message || 'Lỗi không xác định'), 'error');
                }
            } catch (error) {
                viewer.textContent = "Error: " + error;
                showStatus('⚠️ Lỗi kết nối mạng hoặc Server', 'error');
            } finally {
                loading.classList.add('hidden');
            }
        }

        async function importToDB() {
            if (!currentData || !currentType) return;
            
            const btnImport = document.getElementById('btn-import');
            const originalText = btnImport.innerHTML;
            
            if(!confirm(`Bạn có chắc muốn import dữ liệu này vào CSDL?`)) return;

            btnImport.innerHTML = 'Đang xử lý...';
            btnImport.disabled = true;

            try {
                const response = await fetch('{{ route("sync.import") }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
                    body: JSON.stringify({ type: currentType, data: currentData })
                });
                const result = await response.json();
                
                if (result.success) {
                    showStatus(result.message, 'success');
                    if (result.details && result.details.errors && result.details.errors.length > 0) {
                          alert("Có lỗi với một số bản ghi:\n" + result.details.errors.join("\n"));
                    } else {
                        alert(result.message);
                    }
                } else {
                    showStatus('❌ ' + result.message, 'error');
                }
            } catch (error) {
                showStatus('⚠️ Lỗi Import: ' + error, 'error');
            } finally {
                btnImport.innerHTML = originalText;
                btnImport.disabled = false;
            }
        }

        function showStatus(msg, type) {
            const el = document.getElementById('status-msg');
            el.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
            if (type === 'success') el.classList.add('bg-green-100', 'text-green-700');
            else el.classList.add('bg-red-100', 'text-red-700');
            el.textContent = msg;
        }
    </script>
</x-app-layout>