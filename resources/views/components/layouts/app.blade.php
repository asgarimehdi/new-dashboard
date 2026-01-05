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

    {{-- هدر ساده (فعلاً) --}}
    <header class="bg-blue-700 text-white p-4 text-center font-bold">
        Task Manager System (TMS)
    </header>

    {{-- محتوای صفحه --}}
    <main class="py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
