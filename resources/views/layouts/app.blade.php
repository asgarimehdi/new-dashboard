<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">
<script src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>
<style>
    @font-face {
        font-family: 'Vazirmatn';
        src: url('/fonts/vazir/Vazirmatn-Regular.woff2') format('woff2');
        font-weight: normal;
    }

    body {
        font-family: 'Vazirmatn', sans-serif !important;
        direction: rtl;
    }
    
    /* برای اینکه کلاس‌های پیش‌فرض تیلوند هم از این فونت استفاده کنند */
    .font-sans {
        font-family: 'Vazirmatn', sans-serif !important;
    }
</style>
</head>
<body class="bg-gray-100 font-sans">

    <!-- {{-- هدر ساده (فعلاً) --}}
    <header class="bg-blue-700 text-white p-4 text-center font-bold">
        Task Manager System (TMS)
    </header> -->

    {{-- محتوای صفحه --}}
    <main class="py-6">
        @include('layouts.navigation') {{-- این خط مسئول نمایش منوی خروج و نام کاربر است --}}
        {{ $slot }}

    </main>

    @livewireScripts
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:init', () => {
       Livewire.on('swal', (event) => {
           const data = event[0]; // در لاووایر ۳ داده‌ها در اولین ایندکس آرایه هستند
           Swal.fire({
               title: data.title,
               icon: data.icon,
               confirmButtonText: 'تایید',
               timer: 3000,
               toast: true,
               position: 'top-end'
           });
       });
    });
</script>
</body>
</html>
