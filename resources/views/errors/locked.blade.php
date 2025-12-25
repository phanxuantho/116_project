<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống đang bảo trì</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .animation-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">

    <div class="max-w-lg w-full space-y-8 text-center">
        
        {{-- Hình ảnh minh họa (SVG) --}}
        <div class="flex justify-center animation-float">
            <svg class="w-48 h-48 text-indigo-600 dark:text-indigo-400 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4" class="hidden"></path> {{-- Ẩn bớt chi tiết --}}
                <circle cx="12" cy="12" r="9" stroke-width="0.5" class="text-gray-300 dark:text-gray-600" fill="none" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 2a2 2 0 00-2 2v1a1 1 0 01-1 1h1a1 1 0 001-1V3a1 1 0 011-1zm4 0a2 2 0 00-2 2v1a1 1 0 01-1 1h1a1 1 0 001-1V3a1 1 0 011-1z" class="text-orange-400"></path>
            </svg>
        </div>

        {{-- Nội dung thông báo --}}
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 relative overflow-hidden">
            
            {{-- Dải màu trang trí --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

            <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white uppercase tracking-wide">
                Tạm Khóa
            </h2>
            
            <div class="mt-4">
                <p class="text-lg text-gray-600 dark:text-gray-300 font-medium">
                    TRANG NÀY ĐANG TẠM KHÓA HOẶC HỆ THỐNG ĐANG BẢO TRÌ
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 italic">
                    (Vui lòng quay lại sau ít phút hoặc liên hệ quản trị viên nếu cần hỗ trợ gấp)
                </p>
            </div>

            {{-- Các nút hành động --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Về trang chủ
                </a>
                
                <button onclick="location.reload()" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-150 ease-in-out shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Tải lại trang
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500">
            &copy; {{ date('Y') }} Trường Đại học Tây Nguyên. All rights reserved.
        </p>

    </div>

</body>
</html>