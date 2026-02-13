<div x-data="{ open: false }" class="relative">
    <div class="flex items-center justify-between bg-gray-900 p-4 md:hidden">
        <div class="text-white font-bold">پنل TMS</div>
        <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div :class="open ? 'translate-x-0' : 'translate-x-full md:translate-x-0'" 
         class="fixed inset-y-0 right-0 z-50 w-64 bg-gray-900 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 shadow-xl overflow-y-auto">
        
        <div class="p-6 text-white text-xl font-bold border-b border-gray-800 flex justify-between items-center">
            <span>مدیریت سامانه</span>
            <button @click="open = false" class="md:hidden text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="mt-6 px-4 space-y-2 pb-10">
            {{-- منوی عمومی --}}
            <x-nav-link-custom :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                داشبورد
            </x-nav-link-custom>

            <div class="pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">تیکتینگ</div>
            
            <x-nav-link-custom :href="route('tickets.inbox')" :active="request()->routeIs('tickets.inbox')" icon="ticket">
                صندوق تیکت‌ها
            </x-nav-link-custom>

            <x-nav-link-custom :href="route('tickets.create')" :active="request()->routeIs('tickets.create')" icon="plus">
                ثبت تیکت جدید
            </x-nav-link-custom>

            {{-- منوی ادمین --}}
            @if(auth()->user()->hasRole('superadmin'))
                <div class="pt-6 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-t border-gray-800 mt-4">
                    تنظیمات سیستمی
                </div>

                <x-nav-link-custom :href="route('users.index')" :active="request()->routeIs('users.*')" icon="users">
                    مدیریت کاربران
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('roles.manager')" :active="request()->routeIs('roles.*')" icon="shield">
                    سطوح دسترسی
                </x-nav-link-custom>

                <div class="pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">ساختار سازمانی</div>

                <x-nav-link-custom :href="route('units.index')" :active="request()->routeIs('units.index')" icon="office">
                    لیست واحدها
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('units.tree')" :active="request()->routeIs('units.tree')" icon="tree">
                    درختواره واحدها
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('unit-types.index')" :active="request()->routeIs('unit-types.index')" icon="category">
                    انواع واحد
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('unit-types.hierarchy')" :active="request()->routeIs('unit-types.hierarchy')" icon="hierarchy">
                    سلسله مراتب واحدها
                </x-nav-link-custom>

                <div class="pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">تعاریف پایه</div>

                <x-nav-link-custom :href="route('provinces.index')" :active="request()->routeIs('provinces.index')" icon="map">
                    مدیریت استان‌ها
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('cities.index')" :active="request()->routeIs('cities.index')" icon="city">
                    مدیریت شهرها
                </x-nav-link-custom>
            @endif
        </nav>
    </div>

    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden transition-opacity"></div>
</div>