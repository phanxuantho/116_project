{{-- Sử dụng Alpine.js để quản lý trạng thái đóng/mở của các menu con --}}
<div x-data="{ openMenu: '' }" class="flex flex-col w-64 bg-gray-800 text-gray-100 min-h-screen">
    <!-- Logo và Tên ứng dụng -->
    <div class="flex items-center justify-center h-20 shadow-md bg-gray-900">
        <h1 class="text-2xl uppercase text-white font-bold">Ứng dụng 116</h1>
    </div>

    <!-- Danh sách Menu -->
    <ul class="flex flex-col py-4 space-y-1">
    
        <!-- Trang chủ (Dashboard) -->
        <li>
            <a href="{{ route('dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 pr-6
                {{ request()->routeIs('dashboard') ? 'bg-gray-700 border-blue-500' : '' }}">
                <span class="inline-flex justify-center items-center ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">Trang chủ</span>
            </a>
        </li>

        <!-- Menu Cấp 1: Quản lý Sinh viên -->
        <li>
            <button @click="openMenu = (openMenu === 'sv' ? '' : 'sv')" class="w-full relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 pr-6">
                <span class="inline-flex justify-center items-center ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1a6 6 0 006 6z"></path></svg>
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">Quản lý Sinh viên</span>
                <span class="ml-auto">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': openMenu === 'sv'}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </span>
            </button>
            <div x-show="openMenu === 'sv'" x-transition class="bg-gray-700">
                <ul class="flex flex-col pl-8 space-y-1 py-2">
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Thêm mới sinh viên</a></li>
                    <li><a href="{{ route('students.index') }}" class="text-sm text-gray-300 hover:text-white">Cập nhật thông tin sinh viên</a></li>
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Cập nhật trạng thái</a></li>
                </ul>
            </div>
        </li>

        <!-- Menu Cấp 1: Quản lý Cấp phát -->
        <li>
            <button @click="openMenu = (openMenu === 'cp' ? '' : 'cp')" class="w-full relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 pr-6">
                <span class="inline-flex justify-center items-center ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">Quản lý Cấp phát</span>
                 <span class="ml-auto">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': openMenu === 'cp'}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </span>
            </button>
            <div x-show="openMenu === 'cp'" x-transition class="bg-gray-700">
                <ul class="flex flex-col pl-8 space-y-1 py-2">
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">In danh sách rà soát</a></li>
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Cấp phát kinh phí</a></li>
                </ul>
            </div>
        </li>

        <!-- Menu Cấp 1: Tổng hợp Báo cáo -->
        <li>
            <button @click="openMenu = (openMenu === 'bc' ? '' : 'bc')" class="w-full relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 pr-6">
                <span class="inline-flex justify-center items-center ml-4">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">Tổng hợp Báo cáo</span>
                 <span class="ml-auto">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': openMenu === 'bc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </span>
            </button>
            <div x-show="openMenu === 'bc'" x-transition class="bg-gray-700">
                <ul class="flex flex-col pl-8 space-y-1 py-2">
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Báo cáo họp định kì</a></li>
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Báo cáo các địa phương</a></li>
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Báo cáo Bộ GDĐT</a></li>
                    <li><a href="#" class="text-sm text-gray-300 hover:text-white">Báo cáo tổng quan</a></li>
                </ul>
            </div>
        </li>
        <!-- Trang cấu hình hệ thống --> 
        {{-- ========================================= --}}
        {{-- THÊM ĐIỀU KIỆN KIỂM TRA ROLE ADMIN Ở ĐÂY --}}
        {{-- ========================================= --}}
        @if (Auth::check() && Auth::user()->role === 'admin')
        <li>
            <a href="{{ route('settings.edit') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 pr-6
                {{ request()->routeIs('settings.edit') ? 'bg-gray-700 border-blue-500' : '' }}">
                <span class="inline-flex justify-center items-center ml-4">
                    {{-- Icon Cài đặt --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">Cấu hình</span>
            </a>
        </li>
        @endif
        {{-- ========================================= --}}
        {{-- KẾT THÚC ĐIỀU KIỆN --}}
        {{-- ========================================= --}}
        
        
        <!-- Đăng xuất --> 
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();"
                   class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-red-500 pr-6">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Đăng xuất</span>
                </a>
            </form>
        </li>
    </ul>
</div>