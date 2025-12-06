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
                                <p class="text-[10px] text-gray-400 mt-1">*Chỉ dùng cho chức năng lấy từng lớp</p>
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
                        
                        {{-- Nhóm Lấy Toàn Bộ (Mới) --}}
                        <div class="mb-4 bg-yellow-50 p-2 rounded border border-yellow-200">
                            <p class="text-xs font-bold text-yellow-700 mb-2 uppercase">-- TỔNG HỢP (ALL) --</p>
                            <button onclick="fetchAllKQHT()" id="btn-fetch-all" class="w-full px-3 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-left text-sm font-bold shadow flex justify-between items-center transition">
                                <span>🎓 Kết Quả Học Tập (ALL)</span>
                                <span id="loading-percent" class="hidden text-xs bg-yellow-800 px-1 rounded">0%</span>
                            </button>
                        </div>
                        {{-- Đối chiếu trạng thái sinh viên --}}
                        <div class="mb-4 bg-purple-50 p-2 rounded border border-purple-200 mt-4">
                            <p class="text-xs font-bold text-purple-700 mb-2 uppercase">-- RÀ SOÁT DỮ LIỆU --</p>
                            <button onclick="checkAllStudentStatus()" id="btn-check-status" class="w-full px-3 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-left text-sm font-bold shadow flex justify-between items-center transition">
                                <span>🔍 Đối chiếu Trạng thái (ALL)</span>
                                <span id="status-percent" class="hidden text-xs bg-purple-800 text-white px-2 py-0.5 rounded-full">0%</span>
                            </button>
                            <p class="text-[10px] text-purple-600 mt-1 italic">So sánh trạng thái giữa DB Local và API.</p>
                        </div>

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
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase">-- Lớp học (Từng lớp) --</p>
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
                                    <span>🎓 KQHT (1 Lớp)</span>
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
                            <button id="btn-import" onclick="importToDB()" class="hidden items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition shadow-sm animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                                Import vào CSDL
                            </button>
                        </div>
                        
                        <div id="status-msg" class="hidden px-4 py-2 text-sm"></div>
                        
                        {{-- Log process --}}
                        <div id="process-log" class="hidden bg-yellow-50 border-b border-yellow-100 p-2 text-xs font-mono text-yellow-800 max-h-20 overflow-y-auto"></div>

                        <div class="flex-1 p-4 overflow-auto bg-slate-900 text-green-400 font-mono text-xs relative rounded-b-lg">
                            <div id="loading" class="hidden absolute inset-0 flex items-center justify-center bg-slate-900/80 z-10">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-green-500 mx-auto mb-2"></div>
                                    <span id="loading-text" class="text-white font-bold">Đang tải...</span>
                                </div>
                            </div>
                            <pre id="json-viewer">Vui lòng cấu hình tham số và chọn chức năng...</pre>
                        </div>


                        {{-- Khu vực hiển thị kết quả Rà soát --}}
                        <div id="mismatch-container" class="hidden mt-4 bg-white border-t border-gray-200">
                            <div class="px-4 py-2 bg-red-50 border-b border-red-100 flex justify-between items-center">
                                <h3 class="font-bold text-red-700 text-sm">⚠️ Danh sách lệch trạng thái (<span id="mismatch-count">0</span>)</h3>
                                <button onclick="clearMismatchTable()" class="text-xs text-gray-500 hover:text-red-600 underline">Xóa bảng</button>
                            </div>
                            <div class="overflow-x-auto max-h-[400px]">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                                        <tr>
                                            <th class="px-3 py-2">MSSV</th>
                                            <th class="px-3 py-2">Họ Tên</th>
                                            <th class="px-3 py-2 text-blue-600">Local DB</th>
                                            <th class="px-3 py-2 text-green-600">API Đào tạo</th>
                                            <th class="px-3 py-2">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mismatch-tbody" class="divide-y divide-gray-100">
                                        {{-- Dữ liệu sẽ được JS chèn vào đây --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentData = null;
        let currentType = null;

        // --- HÀM 1: LẤY DỮ LIỆU ĐƠN LẺ ---
        async function fetchData(type) {
            setupUIStart();
            const payload = getPayload(type);
            
            try {
                const response = await fetch('{{ route("sync.fetch") }}', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify(payload) 
                });
                const result = await response.json();
                handleResponse(result, type);
            } catch (error) {
                handleError(error);
            } finally {
                setupUIEnd();
            }
        }

        // --- HÀM 2: LẤY TOÀN BỘ DỮ LIỆU (ALL) ---
        async function fetchAllKQHT() {
            if (!confirm("Hệ thống sẽ quét TẤT CẢ các lớp để lấy Kết quả học tập.\nQuá trình này có thể mất thời gian. Bạn có muốn tiếp tục?")) return;

            setupUIStart();
            const viewer = document.getElementById('json-viewer');
            const percentBadge = document.getElementById('loading-percent');
            const logDiv = document.getElementById('process-log');
            const loadingText = document.getElementById('loading-text');

            percentBadge.classList.remove('hidden');
            logDiv.classList.remove('hidden');
            logDiv.innerHTML = "🏁 Bắt đầu lấy danh sách lớp...";
            
            let allResults = [];
            
            try {
                // 1. Lấy danh sách lớp từ DB
                const resList = await fetch('{{ route("sync.get_classes") }}');
                const listData = await resList.json();
                
                if (!listData.success || !listData.codes) throw new Error("Không lấy được danh sách lớp.");
                const classes = listData.codes;
                const total = classes.length;
                
                logDiv.innerHTML = `✅ Tìm thấy ${total} lớp. Đang tải dữ liệu...`;

                // 2. Vòng lặp lấy dữ liệu từng lớp
                for (let i = 0; i < total; i++) {
                    const maLop = classes[i];
                    loadingText.textContent = `Đang tải lớp ${maLop} (${i+1}/${total})`;
                    percentBadge.textContent = `${Math.round(((i+1)/total)*100)}%`;

                    // Gọi API lấy dữ liệu 1 lớp (Tái sử dụng route fetch)
                    const payload = {
                        type: 'kqht_lop',
                        ma_dv: document.getElementById('ma_dv').value,
                        nam_hoc: document.getElementById('nam_hoc').value,
                        hoc_ky: document.getElementById('hoc_ky').value,
                        ma_lop: maLop, // Thay mã lớp trong vòng lặp
                        ma_sv: ''
                    };

                    try {
                        const res = await fetch('{{ route("sync.fetch") }}', {
                            method: 'POST',
                            headers: getHeaders(),
                            body: JSON.stringify(payload)
                        });
                        const json = await res.json();
                        
                        if (json.success) {
                            // Xử lý chuẩn hóa data (Bóc tách .Data nếu có)
                            let dataItems = json.data;
                            if (!Array.isArray(dataItems) && dataItems.Data) {
                                dataItems = dataItems.Data;
                            }
                            
                            if (Array.isArray(dataItems) && dataItems.length > 0) {
                                // Gộp vào mảng tổng
                                allResults = allResults.concat(dataItems);
                                // Cập nhật Viewer liên tục để người dùng thấy dữ liệu đang chạy
                                viewer.textContent = JSON.stringify(allResults, null, 4);
                                // Scroll xuống dưới cùng
                                viewer.parentElement.scrollTop = viewer.parentElement.scrollHeight;
                            }
                        }
                    } catch (err) {
                        console.error(`Lỗi lớp ${maLop}:`, err);
                    }
                    
                    // Nghỉ 50ms để không đơ trình duyệt
                    await new Promise(r => setTimeout(r, 50));
                }

                // 3. Kết thúc vòng lặp
                logDiv.innerHTML = `🎉 Đã hoàn tất! Tổng cộng: ${allResults.length} bản ghi kết quả học tập.`;
                
                // Set dữ liệu vào biến toàn cục để chuẩn bị Import
                currentData = allResults;
                currentType = 'kqht_lop'; // Vẫn dùng type này để Controller biết cách map dữ liệu
                
                showStatus(`✅ Đã tải xong dữ liệu của ${total} lớp. Tổng: ${allResults.length} dòng.`, 'success');
                
                // Hiện nút Import
                const btnImport = document.getElementById('btn-import');
                if (allResults.length > 0) {
                    btnImport.classList.remove('hidden');
                    btnImport.classList.add('flex');
                }

            } catch (error) {
                handleError(error);
            } finally {
                setupUIEnd();
                percentBadge.classList.add('hidden');
            }
        }

        // --- HÀM IMPORT (DÙNG CHUNG CHO CẢ 2) ---
        async function importToDB() {
            if (!currentData || !currentType) return;
            
            const btnImport = document.getElementById('btn-import');
            const originalText = btnImport.innerHTML;
            
            if(!confirm(`Bạn sắp import ${currentData.length} bản ghi vào CSDL.\nThao tác này không thể hoàn tác.`)) return;

            btnImport.innerHTML = '⏳ Đang lưu...';
            btnImport.disabled = true;

            try {
                // Tăng timeout hoặc chia nhỏ nếu dữ liệu quá lớn (tạm thời gửi 1 cục)
                const response = await fetch('{{ route("sync.import") }}', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ type: currentType, data: currentData })
                });
                const result = await response.json();
                
                if (result.success) {
                    showStatus(result.message, 'success');
                    alert(result.message);
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

        // --- CÁC HÀM TIỆN ÍCH (HELPER) ---
        function getPayload(type) {
            return {
                type: type,
                ma_dv: document.getElementById('ma_dv').value,
                nam_hoc: document.getElementById('nam_hoc').value,
                hoc_ky: document.getElementById('hoc_ky').value,
                ma_lop: document.getElementById('ma_lop').value,
                ma_sv: document.getElementById('ma_sv').value
            };
        }

        function getHeaders() {
            return {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
        }

        function setupUIStart() {
            const loading = document.getElementById('loading');
            const btnImport = document.getElementById('btn-import');
            const statusMsg = document.getElementById('status-msg');
            const viewer = document.getElementById('json-viewer');

            loading.classList.remove('hidden');
            btnImport.classList.add('hidden');
            btnImport.classList.remove('flex');
            statusMsg.classList.add('hidden');
            document.getElementById('process-log').classList.add('hidden');
            // Không xóa viewer ngay để người dùng có thể thấy data cũ nếu muốn
        }

        function setupUIEnd() {
            document.getElementById('loading').classList.add('hidden');
        }

        function handleResponse(result, type) {
            const viewer = document.getElementById('json-viewer');
            const btnImport = document.getElementById('btn-import');

            if (result.success) {
                // Chuẩn hóa dữ liệu trả về
                let finalData = result.data;
                if (!Array.isArray(finalData) && finalData.Data) {
                    finalData = finalData.Data;
                }

                currentData = finalData;
                currentType = type;
                viewer.textContent = JSON.stringify(finalData, null, 4);

                let countInfo = Array.isArray(finalData) ? `(${finalData.length} bản ghi)` : '';
                showStatus(`✅ ${result.message} ${countInfo}`, 'success');

                const supportedTypes = ['units', 'lop_khoa', 'sv_lop', 'kqht_lop'];
                if (supportedTypes.includes(type) && Array.isArray(finalData) && finalData.length > 0) {
                    btnImport.classList.remove('hidden');
                    btnImport.classList.add('flex');
                }
            } else {
                viewer.textContent = JSON.stringify(result, null, 4);
                showStatus('❌ ' + (result.message || 'Lỗi không xác định'), 'error');
            }
        }

        function handleError(error) {
            document.getElementById('json-viewer').textContent = "Error: " + error;
            showStatus('⚠️ Lỗi kết nối mạng hoặc Server', 'error');
        }

        function showStatus(msg, type) {
            const el = document.getElementById('status-msg');
            el.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
            if (type === 'success') el.classList.add('bg-green-100', 'text-green-700');
            else el.classList.add('bg-red-100', 'text-red-700');
            el.textContent = msg;
        }

        // === HÀM RÀ SOÁT TRẠNG THÁI ===
        async function checkAllStudentStatus() {
            const delayTime = parseInt(document.getElementById('api_delay').value) || 100;
            
            if (!confirm(`Hệ thống sẽ quét toàn bộ sinh viên để đối chiếu trạng thái.\nĐộ trễ: ${delayTime}ms.\n\nBắt đầu?`)) return;

            // UI Setup
            const btn = document.getElementById('btn-check-status');
            const percentBadge = document.getElementById('status-percent');
            const container = document.getElementById('mismatch-container');
            const tbody = document.getElementById('mismatch-tbody');
            const countSpan = document.getElementById('mismatch-count');
            const logDiv = document.getElementById('process-log'); // Tận dụng log cũ

            btn.disabled = true;
            btn.classList.add('opacity-50');
            percentBadge.classList.remove('hidden');
            container.classList.remove('hidden');
            logDiv.classList.remove('hidden');
            logDiv.innerHTML = "⏳ Đang lấy danh sách sinh viên...";
            
            // Reset bảng kết quả nếu muốn (hoặc giữ lại để cộng dồn)
            // tbody.innerHTML = ''; 
            let mismatchCount = 0;

            try {
                // 1. Lấy danh sách MSSV
                const resList = await fetch('{{ route("sync.get_students") }}');
                const dataList = await resList.json();
                
                if (!dataList.success) throw new Error("Không lấy được danh sách SV.");
                
                const students = dataList.codes;
                const total = students.length;
                logDiv.innerHTML = `✅ Tìm thấy ${total} sinh viên. Đang rà soát...`;

                // 2. Chạy vòng lặp
                for (let i = 0; i < total; i++) {
                    const maSV = students[i];
                    percentBadge.textContent = `${Math.round(((i+1)/total)*100)}%`;

                    try {
                        const resCheck = await fetch('{{ route("sync.check_status") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ ma_sv: maSV })
                        });
                        
                        const result = await resCheck.json();

                        if (result.success && !result.is_match) {
                            // ⚠️ PHÁT HIỆN LỆCH -> Thêm vào bảng
                            mismatchCount++;
                            countSpan.textContent = parseInt(countSpan.textContent) + 1;
                            
                            const row = `
                                <tr class="hover:bg-red-50 transition">
                                    <td class="px-3 py-2 font-mono font-bold">${result.data.ma_sv}</td>
                                    <td class="px-3 py-2">${result.data.ho_ten}</td>
                                    <td class="px-3 py-2 font-bold text-blue-700 bg-blue-50">${result.data.local_status}</td>
                                    <td class="px-3 py-2 font-bold text-green-700 bg-green-50">${result.data.api_status}</td>
                                    <td class="px-3 py-2">
                                        <button onclick="alert('Tính năng cập nhật nhanh đang phát triển')" class="text-xs bg-white border border-gray-300 px-2 py-1 rounded hover:bg-gray-100">Sửa</button>
                                    </td>
                                </tr>
                            `;
                            // Chèn lên đầu bảng
                            tbody.insertAdjacentHTML('afterbegin', row);
                            
                            // Ghi log nhỏ
                            logDiv.innerHTML += `<div class="text-red-500 text-[10px]">⚠️ ${maSV}: ${result.data.local_status} != ${result.data.api_status}</div>`;
                            logDiv.scrollTop = logDiv.scrollHeight;
                        }

                    } catch (err) {
                        console.error(err);
                    }

                    // Delay
                    await new Promise(r => setTimeout(r, delayTime));
                }

                logDiv.innerHTML += `<div class="text-blue-600 font-bold mt-2">🏁 Hoàn tất! Phát hiện ${mismatchCount} trường hợp lệch.</div>`;
                alert(`Rà soát xong! Có ${mismatchCount} sinh viên lệch trạng thái.`);

            } catch (error) {
                alert("Lỗi: " + error.message);
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                percentBadge.classList.add('hidden');
            }
        }

        function clearMismatchTable() {
            document.getElementById('mismatch-tbody').innerHTML = '';
            document.getElementById('mismatch-count').textContent = '0';
        }



    </script>
</x-app-layout>