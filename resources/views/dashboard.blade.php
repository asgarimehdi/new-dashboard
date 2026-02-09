<x-app-layout>
    {{-- این بخش به صورت خودکار منوی نویگیشن را از فایل layouts.navigation لود می‌کند --}}
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-right">
            {{ __('داشبورد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-right">
                    {{ __("خوش آمدید! شما وارد سیستم شده‌اید.") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>