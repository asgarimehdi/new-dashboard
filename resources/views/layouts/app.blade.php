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
<div class="min-h-screen bg-gray-100 flex">
    {{-- سایدبار --}}
    @include('layouts.sidebar')

    {{-- محتوای اصلی سمت چپ --}}
    <div class="flex-1 flex flex-col min-w-0">
      {{--   @include('layouts.navigation')--}} 

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="p-6">
            {{ $slot }}
        </main>
    </div>
</div>
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
