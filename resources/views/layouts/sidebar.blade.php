<div x-data="{ open: false, isCollapsed: false }" class="relative flex min-h-screen bg-gray-100">
    
    <div class="fixed top-0 right-0 left-0 flex items-center justify-between bg-gray-900 p-4 md:hidden z-[60] shadow-lg">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-xs shadow-lg shadow-indigo-500/20">TMS</div>
            <span class="text-white font-black text-sm tracking-tighter">مدیریت سامانه</span>
        </div>
        <button @click="open = !open" class="text-gray-400 hover:text-white transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <aside 
        :class="{
            'translate-x-0': open, 
            'translate-x-full md:translate-x-0': !open,
            'w-72': !isCollapsed,
            'w-24': isCollapsed
        }"
        class="fixed inset-y-0 right-0 z-50 bg-gray-900 transition-all duration-300 ease-in-out md:relative shadow-2xl flex flex-col border-l border-gray-800"
    >
        <div class="p-6 text-center border-b border-gray-800/50 bg-gray-950/30">
            <h1 :class="isCollapsed ? 'text-lg tracking-widest' : 'text-3xl tracking-[0.3em]'" 
                class="font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 transition-all">
                TMS
            </h1>
        </div>

        <button @click="isCollapsed = !isCollapsed" 
                class="hidden md:flex absolute -left-3 top-12 bg-indigo-600 text-white rounded-full w-6 h-6 items-center justify-center z-[70] hover:bg-indigo-500 shadow-lg shadow-indigo-900/40 transition-all">
            <svg class="w-4 h-4 transition-transform duration-500" :class="isCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <div class="p-4 mt-2 shrink-0 border-b border-gray-800/50 bg-gray-950/20">
            <div :class="isCollapsed ? 'flex-col items-center gap-4' : 'flex-row items-center gap-3'" class="flex transition-all duration-300">
                
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-800 flex items-center justify-center text-white font-black shadow-xl shrink-0 ring-2 ring-white/5">
                    {{ mb_substr(auth()->user()->full_name, 0, 1) }}
                </div>
                
                <div x-show="!isCollapsed" x-transition.opacity.duration.300ms class="flex flex-col min-w-0 flex-1">
                    <span class="text-[11px] font-black text-white truncate">{{ auth()->user()->full_name }}</span>
                    <span class="text-[9px] font-bold text-gray-500 truncate mt-0.5">{{ auth()->user()->unit->name ?? 'بدون واحد' }}</span>
                    
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-[10px] font-black text-red-400 hover:text-red-300 flex items-center gap-1 transition-colors group">
                            <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            خروج از حساب
                        </button>
                    </form>
                </div>

                <div x-show="isCollapsed" class="mt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <nav class="mt-4 px-3 space-y-1.5 flex-1 pb-10 overflow-x-hidden overflow-y-auto custom-scrollbar">
            
            {{-- منوی عمومی --}}
            <div x-show="!isCollapsed" class="px-4 py-2 text-[10px] font-black text-gray-600 uppercase tracking-widest">داشبورد</div>
            
            <x-nav-link-custom :href="route('dashboard')" wire:navigate :active="request()->routeIs('dashboard')" icon="home">
                <span x-show="!isCollapsed" class="truncate font-bold">میز کار</span>
            </x-nav-link-custom>

            {{-- بخش تیکتینگ --}}
            <div x-show="!isCollapsed" class="px-4 pt-4 pb-2 text-[10px] font-black text-gray-600 uppercase tracking-widest">تیکتینگ</div>
            
            @role('admin|superadmin')
            <x-nav-link-custom :href="route('tickets.monitoring')" wire:navigate :active="request()->routeIs('tickets.monitoring')" icon="chart-bar">
                <span x-show="!isCollapsed" class="truncate">مانیتورینگ کل</span>
            </x-nav-link-custom>
            @endrole

            <x-nav-link-custom :href="route('tickets.inbox')" wire:navigate :active="request()->routeIs('tickets.inbox')" icon="ticket">
                <span x-show="!isCollapsed" class="truncate">صندوق تیکت‌ها</span>
            </x-nav-link-custom>

            <x-nav-link-custom :href="route('tickets.create')" wire:navigate :active="request()->routeIs('tickets.create')" icon="plus">
                <span x-show="!isCollapsed" class="truncate">ثبت تیکت جدید</span>
            </x-nav-link-custom>

            {{-- منوی ادمین --}}
            @if(auth()->user()->hasRole('superadmin'))
                <div x-show="!isCollapsed" class="px-4 pt-6 pb-2 text-[10px] font-black text-gray-600 uppercase tracking-widest border-t border-gray-800/50 mt-4 text-indigo-400">تنظیمات سیستمی</div>

                <x-nav-link-custom :href="route('users.index')" wire:navigate :active="request()->routeIs('users.*')" icon="users">
                    <span x-show="!isCollapsed" class="truncate">مدیریت کاربران</span>
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('roles.manager')" wire:navigate :active="request()->routeIs('roles.*')" icon="shield">
                    <span x-show="!isCollapsed" class="truncate">سطوح دسترسی</span>
                </x-nav-link-custom>

                <div x-show="!isCollapsed" class="px-4 pt-4 pb-2 text-[10px] font-black text-gray-600 uppercase tracking-widest">ساختار سازمانی</div>

                <x-nav-link-custom :href="route('units.index')" wire:navigate :active="request()->routeIs('units.index')" icon="office">
                    <span x-show="!isCollapsed" class="truncate">لیست واحدها</span>
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('units.tree')" wire:navigate :active="request()->routeIs('units.tree')" icon="tree">
                    <span x-show="!isCollapsed" class="truncate">درختواره واحدها</span>
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('unit-types.index')" wire:navigate :active="request()->routeIs('unit-types.index')" icon="category">
                    <span x-show="!isCollapsed" class="truncate">انواع واحد</span>
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('unit-types.hierarchy')" wire:navigate :active="request()->routeIs('unit-types.hierarchy')" icon="hierarchy">
                    <span x-show="!isCollapsed" class="truncate">سلسله مراتب</span>
                </x-nav-link-custom>

                <div x-show="!isCollapsed" class="px-4 pt-4 pb-2 text-[10px] font-black text-gray-600 uppercase tracking-widest">تعاریف پایه</div>

                <x-nav-link-custom :href="route('provinces.index')" wire:navigate :active="request()->routeIs('provinces.index')" icon="map">
                    <span x-show="!isCollapsed" class="truncate">مدیریت استان‌ها</span>
                </x-nav-link-custom>

                <x-nav-link-custom :href="route('cities.index')" wire:navigate :active="request()->routeIs('cities.index')" icon="city">
                    <span x-show="!isCollapsed" class="truncate">مدیریت شهرها</span>
                </x-nav-link-custom>
            @endif
        </nav>
    </aside>

    <div x-show="open" @click="open = false" 
         class="fixed inset-0 bg-black/60 z-40 md:hidden backdrop-blur-sm transition-all"></div>
</div>