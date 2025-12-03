<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Quản lý Lớp Học
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between mb-4">
                    <form method="GET" class="flex">
                        <input type="text" name="search" placeholder="Tìm mã hoặc tên lớp..." class="border rounded-l px-3 py-2" value="{{ request('search') }}">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r">Tìm</button>
                    </form>
                    <a href="{{ route('classes.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        + Thêm Lớp Mới
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
                @endif

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã Lớp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên Lớp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngành / Khoa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khóa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($classes as $class)
                        <tr>
                            <td class="px-6 py-4">{{ $class->class_code }}</td>
                            <td class="px-6 py-4 font-medium">{{ $class->class_name }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm">{{ $class->major->major_name }}</div>
                                <div class="text-xs text-gray-500">{{ $class->faculty->faculty_name }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $class->course_year }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $class->class_status === 'Đang học' ? 'bg-green-100 text-green-800' : ($class->class_status === 'Đã tốt nghiệp' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $class->class_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('classes.edit', $class->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $classes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>