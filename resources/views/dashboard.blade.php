<x-app-layout>
   <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hệ thống') }}
        </h2>
    </x-slot> 

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Phần nội dung chào mừng được thiết kế lại --}}
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white p-8 text-center shadow-lg">
                        <div class="flex justify-center mb-4">
                            {{-- Icon biểu trưng --}}
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222 4 2.222V20"></path></svg>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">
                            Chào mừng bạn đã đăng nhập thành công!
                        </h3>
                        <p class="text-md md:text-lg text-indigo-100">
                            Hệ thống quản lý sinh viên sư phạm nhận hỗ trợ theo Nghị định 116/2020/NĐ-CP
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('students.index') }}" class="bg-white text-blue-600 font-semibold py-2 px-6 rounded-full hover:bg-gray-100 transition duration-300 ease-in-out shadow">
                                Bắt đầu quản lý
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
