<div class="mb-6">
    <h3 class="text-lg font-bold mb-2 text-blue-600">Bước 2: Chọn Lớp thụ hưởng</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Cột Lớp Đang học --}}
        <div class="border p-4 rounded bg-green-50">
            <h4 class="font-bold text-green-800 mb-2">A. Các lớp Đang học</h4>
            <p class="text-xs text-gray-600 mb-2 italic">(Hệ thống sẽ lọc: Status="Đang học" & Funding="Đang nhận")</p>
            <div class="h-64 overflow-y-auto bg-white border p-2">
                {{-- Toggle chọn tất cả --}}
                <label class="flex items-center space-x-2 p-1 border-b mb-1 pb-1">
                    <input type="checkbox" onchange="toggleCheckboxes(this, '.active-class')" class="rounded">
                    <span class="font-bold text-sm">Chọn tất cả</span>
                </label>

                @foreach($classes->where('class_status', 'Đang học') as $class)
                    <label class="flex items-center space-x-2 p-1 hover:bg-gray-100">
                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" class="active-class rounded text-green-600">
                        <span class="text-sm">{{ $class->class_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Cột Lớp Đã tốt nghiệp --}}
        <div class="border p-4 rounded bg-orange-50">
            <h4 class="font-bold text-orange-800 mb-2">B. Các lớp Đã tốt nghiệp</h4>
            <p class="text-xs text-gray-600 mb-2 italic">(Hệ thống sẽ lọc: Status="Gia hạn")</p>
            <div class="h-64 overflow-y-auto bg-white border p-2">
                <label class="flex items-center space-x-2 p-1 border-b mb-1 pb-1">
                    <input type="checkbox" onchange="toggleCheckboxes(this, '.grad-class')" class="rounded">
                    <span class="font-bold text-sm">Chọn tất cả</span>
                </label>

                @foreach($classes->where('class_status', 'Đã tốt nghiệp') as $class)
                    <label class="flex items-center space-x-2 p-1 hover:bg-gray-100">
                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" class="grad-class rounded text-orange-600">
                        <span class="text-sm">{{ $class->class_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCheckboxes(source, selector) {
        const checkboxes = document.querySelectorAll(selector);
        checkboxes.forEach(cb => cb.checked = source.checked);
    }
</script>