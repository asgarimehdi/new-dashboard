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
<body class="antialiased font-sans">
    <div class="min-h-screen bg-[#f1f5f9] flex overflow-x-hidden">
        
        {{-- سایدبار --}}
        @include('layouts.sidebar')

        {{-- محتوای اصلی --}}
        <div class="flex-1 flex flex-col min-w-0">
            
            @if (isset($header))
                <header class="bg-white/70 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="p-4 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
    
    @livewireScripts
    {{-- بقیه اسکریپت‌ها --}}

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
