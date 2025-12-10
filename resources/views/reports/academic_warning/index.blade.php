<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cảnh báo Kết quả Học tập (TK04)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- FORM LỌC --}}
                <form method="GET" action="{{ route('reports.academic_warning.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="semester_id" :value="__('Học kỳ (*)')" />
                            <select name="semester_id" id="semester_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ request('semester_id', $selectedSemester->id ?? '') == $sem->id ? 'selected' : '' }}>
                                        HK {{ $sem->semester_number }} ({{ $sem->schoolYear->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="faculty_id" :value="__('Khoa')" />
                            <select name="faculty_id" id="faculty_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">-- Tất cả Khoa --</option>
                                @foreach($faculties as $fac)
                                    <option value="{{ $fac->id }}" {{ request('faculty_id') == $fac->id ? 'selected' : '' }}>
                                        {{ $fac->faculty_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="class_id" :value="__('Lớp')" />
                            <select name="class_id" id="class_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Tất cả Lớp --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->class_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Trạng thái SV')" />
                            <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <x-primary-button type="submit">Lọc Dữ Liệu</x-primary-button>
                    </div>
                </form>

                {{-- DANH SÁCH KẾT QUẢ --}}
                @if($students->isNotEmpty())
                    <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                        <p class="font-bold">Kết quả:</p>
                        <p>Tìm thấy {{ $students->count() }} sinh viên có điểm TB < 2.0 trong học kỳ này.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">STT</th>
                                    <th class="px-4 py-2 border">MSSV</th>
                                    <th class="px-4 py-2 border">Họ tên</th>
                                    <th class="px-4 py-2 border">Ngày sinh</th>
                                    <th class="px-4 py-2 border">Lớp</th>
                                    <th class="px-4 py-2 border">TC ĐK</th>
                                    <th class="px-4 py-2 border">TC TL</th>
                                    <th class="px-4 py-2 border text-red-600">Điểm TB</th>
                                    <th class="px-4 py-2 border">Xếp loại</th>
                                    <th class="px-4 py-2 border">Điểm RL</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($students as $index => $student)
                                    @php 
                                        $result = $student->academicResults->first();
                                        $score = $result ? $result->academic_score : 0;
                                        $rank = ($score < 1.0) ? 'Kém' : 'Yếu';
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 border text-center">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border text-center">{{ $student->student_code }}</td>
                                        <td class="px-4 py-2 border">{{ $student->full_name }}</td>
                                        <td class="px-4 py-2 border text-center">{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 border">{{ $student->class->class_name }}</td>
                                        <td class="px-4 py-2 border text-center">{{ $result->registered_credits ?? '' }}</td>
                                        <td class="px-4 py-2 border text-center">{{ $result->accumulated_credits ?? '' }}</td>
                                        <td class="px-4 py-2 border text-center font-bold text-red-600">{{ $score }}</td>
                                        <td class="px-4 py-2 border text-center">
                                            <span class="px-2 py-1 rounded text-xs text-white {{ $score < 1.0 ? 'bg-red-500' : 'bg-orange-400' }}">
                                                {{ $rank }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border text-center">{{ $result->conduct_score ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- THANH CÔNG CỤ EXPORT / PRINT --}}
                    <div class="mt-8 pt-4 border-t border-gray-200 flex flex-wrap gap-4 justify-center bg-gray-50 p-4 rounded-lg">
                        
                        {{-- Nhóm 1: Thao tác với danh sách đang lọc --}}
                        <div class="flex gap-2">
                            <form action="{{ route('reports.academic_warning.export') }}" method="GET" target="_blank">
                                @foreach(request()->all() as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                    📥 Xuất Excel (Đang lọc)
                                </button>
                            </form>

                            <form action="{{ route('reports.academic_warning.print') }}" method="GET" target="_blank">
                                @foreach(request()->all() as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    🖨️ In (Đang lọc)
                                </button>
                            </form>
                        </div>

                        <div class="w-px bg-gray-300 mx-2"></div> {{-- Divider --}}

                        {{-- Nhóm 2: Thao tác với TẤT CẢ (bỏ qua lọc Lớp/Khoa) --}}
                        <div class="flex gap-2">
                            <form action="{{ route('reports.academic_warning.export') }}" method="GET" target="_blank">
                                <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="scope" value="all"> {{-- Cờ báo hiệu lấy tất cả --}}
                                <button type="submit" class="px-4 py-2 bg-green-800 text-white rounded hover:bg-green-900 border border-green-900">
                                    📥 Xuất Tất Cả Lớp
                                </button>
                            </form>

                            <form action="{{ route('reports.academic_warning.print') }}" method="GET" target="_blank">
                                <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="scope" value="all">
                                <button type="submit" class="px-4 py-2 bg-blue-800 text-white rounded hover:bg-blue-900 border border-blue-900">
                                    🖨️ In Tất Cả Lớp
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <div class="mt-4 p-4 text-center text-gray-500 italic">
                        Không tìm thấy sinh viên nào bị cảnh báo học tập (điểm < 2.0) với điều kiện lọc này.
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>