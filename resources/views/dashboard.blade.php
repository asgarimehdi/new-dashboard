<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse justify-between items-center ">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span>میز کار من</span>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </h2>
            <div class="text-sm text-gray-500 italic">
                واحد: {{ auth()->user()->unit->name ?? 'بدون واحد' }}
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 md:px-8 " dir="rtl">
        <div class="max-w-7xl mx-auto space-y-8 text-right">

            {{-- ۱. کارت‌های آماری --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-2xl shadow-sm border-r-4 border-amber-500 flex items-center justify-between transition-transform hover:scale-[1.02]">
                    <div>
                        <p class="text-sm text-gray-500 font-medium italic">تیکت‌های ورودی (جدید)</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['pending_inbox'] }}</h3>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-xl text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border-r-4 border-emerald-500 flex items-center justify-between transition-transform hover:scale-[1.02]">
                    <div>
                        <p class="text-sm text-gray-500 font-medium italic">ارسالی‌های تایید شده</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['accepted_outbox'] }}</h3>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border-r-4 border-red-500 flex items-center justify-between transition-transform hover:scale-[1.02]">
                    <div>
                        <p class="text-sm text-gray-500 font-medium italic">ارسالی‌های رد شده</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['rejected_outbox'] }}</h3>
                    </div>
                    <div class="bg-red-50 p-3 rounded-xl text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ۲. جدول تیکت‌های اخیر (ورودی و خروجی) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h4 class="font-bold text-gray-700 italic flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                            آخرین فعالیت‌ها
                        </h4>
                        <a href="{{ route('tickets.inbox') }}" class="text-blue-600 text-sm hover:underline font-bold">مشاهده صندوق تیکت</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead class="bg-gray-50/50">
                                <tr class="text-gray-400 text-xs uppercase">
                                    <th class="p-4 font-semibold text-right">موضوع و زمان</th>
                                    <th class="p-4 font-semibold text-center">جزئیات طرفین</th>
                                    <th class="p-4 font-semibold text-center">وضعیت</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($stats['recent_tickets'] as $ticket)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4">
                                        <div class="text-sm font-bold text-gray-800">{{ $ticket->subject }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1 italic">{{ $ticket->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($ticket->user_id == auth()->id())
                                        <div class="flex flex-col items-center">
                                            <span class="text-blue-600 text-[11px] font-bold bg-blue-50 px-2 py-0.5 rounded">خروجی به:</span>
                                            <span class="text-gray-600 text-xs mt-1">{{ $ticket->unit->name ?? '---' }}</span>
                                        </div>
                                        @else
                                        <div class="flex flex-col items-center">
                                            <span class="text-purple-600 text-[11px] font-bold bg-purple-50 px-2 py-0.5 rounded">ورودی از:</span>
                                            <span class="text-gray-600 text-xs mt-1">{{ $ticket->user->name ?? 'سیستمی' }}</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @switch($ticket->status)
                                        @case('created')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            جدید
                                        </span>
                                        @break
                                        @case('forwarded')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">
                                            ارجاع شده
                                        </span>
                                        @break
                                        @case('accepted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                            تایید شده
                                        </span>
                                        @break
                                        @case('rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                            رد شده
                                        </span>
                                        @break
                                        @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                            {{ $ticket->status }}
                                        </span>
                                        @endswitch
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-12 text-center">
                                        <div class="flex flex-col items-center text-gray-400 italic">
                                            <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <span>هنوز هیچ تیکتی ثبت یا دریافت نشده است.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ۳. ستون دسترسی سریع --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h4 class="font-bold text-gray-700 mb-6 flex items-center gap-2 italic">
                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                            دسترسی سریع
                        </h4>
                        <div class="space-y-3">
                            <a href="{{ route('tickets.create') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all group">
                                <span class="text-sm font-bold italic group-hover:translate-x-[-5px] transition-transform">ثبت تیکت جدید</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </a>

                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-800 hover:text-white transition-all group">
                                <span class="text-sm font-bold italic group-hover:translate-x-[-5px] transition-transform">تنظیمات حساب</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- ویجت اطلاعات سیستم (فقط برای ادمین) --}}
                    @if(auth()->user()->hasRole('superadmin'))
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-lg p-6 text-white">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-white/10 rounded-lg">
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3.005 3.005 0 013.75-2.906z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-sm italic">وضعیت کل سیستم</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-xs">
                                <span class="opacity-70 italic">کل کاربران ثبت شده:</span>
                                <span class="font-bold">{{ $stats['total_users'] }}</span>
                            </div>
                            <div class="w-full bg-white/10 rounded-full h-1.5">
                                <div class="bg-amber-400 h-1.5 rounded-full" style="width: 70%"></div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>