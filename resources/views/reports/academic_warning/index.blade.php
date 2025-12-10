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
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Bộ lọc Dữ liệu</h3>
                </div>

                <form method="GET" action="{{ route('reports.academic_warning.index') }}" class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        
                        <div>
                            <x-input-label for="semester_id" :value="__('Học kỳ (*)')" />
                            <x-select-input name="semester_id" id="semester_id" class="block mt-1 w-full" onchange="this.form.submit()">
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ request('semester_id', $selectedSemester->id ?? '') == $sem->id ? 'selected' : '' }}>
                                        HK {{ $sem->semester_number }} ({{ $sem->schoolYear->name }})
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="faculty_id" :value="__('Khoa')" />
                            <x-select-input name="faculty_id" id="faculty_id" class="block mt-1 w-full" onchange="this.form.submit()">
                                <option value="">-- Tất cả Khoa --</option>
                                @foreach($faculties as $fac)
                                    <option value="{{ $fac->id }}" {{ request('faculty_id') == $fac->id ? 'selected' : '' }}>
                                        {{ $fac->faculty_name }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="class_id" :value="__('Lớp')" />
                            <x-select-input name="class_id" id="class_id" class="block mt-1 w-full">
                                <option value="">-- Tất cả Lớp --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->class_name }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Trạng thái SV')" />
                            <x-select-input name="status" id="status" class="block mt-1 w-full">
                                <option value="">-- Tất cả --</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button type="submit">
                            {{ __('Lọc Dữ Liệu') }}
                        </x-primary-button>
                    </div>
                </form>

                {{-- DANH SÁCH KẾT QUẢ --}}
                @if($students->isNotEmpty())
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 text-yellow-700 dark:text-yellow-200">
                        <p class="font-bold">Kết quả:</p>
                        <p>Tìm thấy <b>{{ $students->count() }}</b> sinh viên có điểm TB < 2.0 trong học kỳ này.</p>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">STT</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MSSV</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Họ tên</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ngày sinh</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lớp</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">TC ĐK</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">TC TL</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-red-600 uppercase tracking-wider">Điểm TB</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Xếp loại</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Điểm RL</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($students as $index => $student)
                                    @php 
                                        $result = $student->academicResults->first();
                                        $score = $result ? $result->academic_score : 0;
                                        $rank = ($score < 1.0) ? 'Kém' : 'Yếu';
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">{{ $loop->iteration }}
                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">{{ $student->student_code }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->full_name }}</td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $student->class->class_name }}</td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">{{ $result->registered_credits ?? '' }}</td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">{{ $result->accumulated_credits ?? '' }}</td>
                                        <td class="px-4 py-3 text-center text-sm font-bold text-red-600">{{ $score }}</td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <span class="px-2 py-1 rounded text-xs font-semibold text-white {{ $score < 1.0 ? 'bg-red-500' : 'bg-orange-400' }}">
                                                {{ $rank }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">{{ $result->conduct_score ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- THANH CÔNG CỤ EXPORT / PRINT --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 justify-center bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        
                        {{-- Nhóm 1: Thao tác với danh sách đang lọc --}}
                        <div class="flex gap-2">
                            <form action="{{ route('reports.academic_warning.export') }}" method="GET" target="_blank">
                                @foreach(request()->all() as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none transition ease-in-out duration-150">
                                    📥 Xuất Excel (Đang lọc)
                                </button>
                            </form>

                            <form action="{{ route('reports.academic_warning.print') }}" method="GET" target="_blank">
                                @foreach(request()->all() as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                                    🖨️ In (Đang lọc)
                                </button>
                            </form>
                        </div>

                        <div class="w-px bg-gray-300 dark:bg-gray-600 mx-2 hidden md:block"></div> {{-- Divider --}}

                        {{-- Nhóm 2: Thao tác với TẤT CẢ (bỏ qua lọc Lớp/Khoa) --}}
                        <div class="flex gap-2">
                            <form action="{{ route('reports.academic_warning.export') }}" method="GET" target="_blank">
                                <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="scope" value="all">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-900 focus:bg-green-900 active:bg-green-900 focus:outline-none transition ease-in-out duration-150">
                                    📥 Xuất Tất Cả Lớp
                                </button>
                            </form>

                            <form action="{{ route('reports.academic_warning.print') }}" method="GET" target="_blank">
                                <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="scope" value="all">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-900 focus:bg-blue-900 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                                    🖨️ In Tất Cả Lớp
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <div class="mt-4 p-8 text-center text-gray-500 dark:text-gray-400 italic bg-gray-50 dark:bg-gray-700 rounded-lg">
                        Không tìm thấy sinh viên nào bị cảnh báo học tập (điểm < 2.0) với điều kiện lọc này.
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>