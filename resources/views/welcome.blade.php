<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ứng dụng quản lý sinh viên 116/2016/NĐ-CP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5; 
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .main-content-card {
            animation: fadeInUp 0.8s ease-out forwards;
            /* NỀN ĐẬM HƠN */
            background-color: #f1f3f5; 
        }

        /* VĂN BẢN VIẾT HOA & IN ĐẬM */
        #main-title {
            font-size: 1.1em;
            font-weight: bold; /* Bold nhất */
            text-transform: uppercase; /* Viết hoa */
            letter-spacing: 1.3px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
            background: #0c498d;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        #sub-title {
            font-weight: 600; /* Bold */
            text-transform: uppercase; /* Viết hoa */
            letter-spacing: 2px;
        }

        #logo-container {
            transition: all 0.3s ease-out;
            will-change: transform; 
        }
        
        #logo-image {
            transition: all 0.3s ease-out;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.05));
        }

        #logo-container:hover #logo-image {
            filter: drop-shadow(0 8px 20px rgba(16, 124, 65, 0.2));
            transform: scale(1.05);
        }

        .custom-button {
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .custom-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4" style="animation: fadeInUp 0.6s ease-out forwards;">
                @auth
                    <a
                        href="{{ url('/dashboard') }}"
                        class="custom-button inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                    >
                        Bảng điều khiển
                    </a>
                 @else
                    <a
                        href="{{ url('/student-update') }}"
                        class="custom-button inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Cập nhật thông tin SV
                    </a> 
                    <a
                        href="{{ url('/graduate-employment') }}"
                        class="custom-button inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Khai báo việc làm
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="custom-button inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Đăng nhập
                    </a>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            style="display: none;"
                            class="custom-button inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">                           
                            Đăng ký
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>
    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="main-content-card flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row rounded-lg shadow-xl overflow-hidden">
            <div class="flex-1 p-6 pb-12  flex flex-col justify-center">
                <h1 id="main-title" class="mb-4 text-4xl text-center">
                    Ứng dụng quản lý sinh viên sư phạm </br>
                    trường đại học tây nguyên
                </h1>
                <p id="sub-title" class="text-center text-gray-600 text-base">
                    Nhận hỗ trợ theo Nghị định 116/2016/NĐ-CP
                </p>
            </div>
            <div id="logo-container" class="relative aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 flex items-center justify-center p-6">
                <img id="logo-image" src="https://www.ttn.edu.vn/images/Icon/logo.png" alt="Logo ĐHTN" class="h-auto max-w-xs">
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const logoContainer = document.getElementById('logo-container');
            if(logoContainer) {
                logoContainer.addEventListener('mousemove', (e) => {
                    const rect = logoContainer.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    const rotateX = ((y - centerY) / centerY) * -10;
                    const rotateY = ((x - centerX) / centerX) * 10;

                    logoContainer.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
                });

                logoContainer.addEventListener('mouseleave', () => {
                    logoContainer.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
                });
            }
        });
    </script>
</body>
</html>