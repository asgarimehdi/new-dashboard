<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse justify-between items-center ">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span>میز کار من</span>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </h2>
           
        </div>
    </x-slot>

    <div class="py-6 px-4 md:px-8 " dir="rtl">
        <div class="max-w-7xl mx-auto space-y-8 text-right">

            {{-- ۱. کارت‌های آماری --}}
            <div class="space-y-8 " dir="rtl">

                {{-- ردیف اول: مدیریت تیکت‌های ورودی و وظایف من --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-400 mb-4 flex items-center gap-2 italic">
                        <span class="w-1 h-4 bg-purple-600 rounded-full"></span>
                        میز کار و وظایف من (ورودی)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border-r-4 border-purple-500 transition-all hover:shadow-md">
                            <p class="text-xs text-gray-500 font-medium italic">تیکت‌های جدید واحد</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-2">{{ $stats['pending_inbox'] }}</h3>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border-r-4 border-blue-500 transition-all hover:shadow-md">
                            <p class="text-xs text-gray-500 font-medium italic">در حال پیگیری توسط من</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-2">{{ $stats['my_in_progress'] }}</h3>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border-r-4 border-emerald-500 transition-all hover:shadow-md">
                            <p class="text-xs text-gray-500 font-medium italic">انجام شده توسط من</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-2">{{ $stats['my_completed'] }}</h3>
                        </div>
                    </div>
                </div>

                {{-- ردیف دوم: وضعیت تیکت‌های ارسالی من به بقیه --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-400 mb-4 flex items-center gap-2 italic">
                        <span class="w-1 h-4 bg-amber-500 rounded-full"></span>
                        تیکت‌های ارسالی من (خروجی)
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-500 font-bold">در انتظار تایید مقصد</p>
                            <h4 class="text-lg font-bold text-amber-600 mt-1">{{ $stats['my_outbox_waiting'] }}</h4>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-500 font-bold">تایید شده / در حال انجام</p>
                            <h4 class="text-lg font-bold text-blue-600 mt-1">{{ $stats['my_outbox_accepted'] }}</h4>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <p class="text-[10px] text-gray-500 font-bold">رد شده توسط مقصد</p>
                            <h4 class="text-lg font-bold text-red-600 mt-1">{{ $stats['my_outbox_rejected'] }}</h4>
                        </div>

                        <div class="bg-emerald-600 p-4 rounded-xl border border-emerald-700 shadow-sm shadow-emerald-200">
                            <p class="text-[10px] text-white/80 font-bold">تکمیل و نهایی شده</p>
                            <h4 class="text-lg font-bold text-white mt-1">{{ $stats['my_outbox_done'] }}</h4>
                        </div>
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
                                        <div class="text-sm font-bold text-gray-800" title="{{ $ticket->subject }}">
                                            {{ str($ticket->subject)->limit(15, '...') }}
                                        </div>
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
                                            <span class="text-gray-600 text-xs mt-1">{{ $ticket->user->full_name ?? 'سیستمی' }}</span>
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
                                         @case('completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-red-700">
                                            بسته شده
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