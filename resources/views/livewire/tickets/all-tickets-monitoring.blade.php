<div class="p-6 " dir="rtl">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-xl font-bold text-gray-800 italic border-r-4 border-indigo-600 pr-3">مانیتورینگ کل تیکت‌های سیستم</h2>

            <div class="flex gap-2 w-full md:w-auto">
                {{-- جستجوی متن --}}
                <input type="text" wire:model.live="search" placeholder="جستجوی کد یا موضوع..." class="border-gray-200 rounded-xl text-sm w-full md:w-64 focus:ring-indigo-500">
            </div>
        </div>

        {{-- بخش فیلتر پیشرفته واحد --}}
        <div class="bg-indigo-900 rounded-2xl p-6 mb-6 text-white shadow-lg shadow-indigo-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs opacity-70 mb-2">فیلتر بر اساس واحد عملیاتی:</label>
                    <div class="relative">
                        <input type="text" wire:model.live="unitSearch"
                            class="w-full bg-white/10 border-white/20 rounded-xl p-2.5 text-sm placeholder-white/40 text-white focus:bg-white focus:text-gray-900 transition-all"
                            placeholder="نام واحد را تایپ کنید...">

                        @if(!empty($unitSearch) && !empty($filterUnits))
                        <div class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-1 overflow-hidden border border-gray-100">
                            @foreach($filterUnits as $u)
                            <button wire:click="selectUnitForFilter({{ $u->id }})"
                                class="w-full text-right px-4 py-3 hover:bg-indigo-50 text-gray-700 text-sm border-b last:border-0">
                                {{ $u->name }}
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button wire:click="$set('statusFilter', 'all')" class="px-4 py-2 rounded-lg text-xs font-bold {{ $statusFilter === 'all' ? 'bg-white text-indigo-900' : 'bg-white/10 text-white hover:bg-white/20' }}">همه وضعیت‌ها</button>
                    <button wire:click="$set('statusFilter', 'pending')" class="px-4 py-2 rounded-lg text-xs font-bold {{ $statusFilter === 'pending' ? 'bg-amber-400 text-indigo-900' : 'bg-white/10 text-white hover:bg-white/20' }}">در انتظار</button>
                    <button wire:click="$set('statusFilter', 'accepted')" class="px-4 py-2 rounded-lg text-xs font-bold {{ $statusFilter === 'accepted' ? 'bg-blue-400 text-indigo-900' : 'bg-white/10 text-white hover:bg-white/20' }}">در حال انجام</button>
                    <button wire:click="$set('statusFilter', 'completed')" class="px-4 py-2 rounded-lg text-xs font-bold {{ $statusFilter === 'completed' ? 'bg-emerald-400 text-indigo-900' : 'bg-white/10 text-white hover:bg-white/20' }}">تکمیل شده</button>
                </div>
            </div>

            @if($selectedUnitId)
            <div class="mt-4 flex items-center gap-2 text-xs">
                <span class="opacity-70">در حال مشاهده تیکت‌های واحد:</span>
                <span class="bg-amber-400 text-indigo-900 px-3 py-1 rounded-full font-bold">{{ $currentUnit->name }}</span>
                <button wire:click="$set('selectedUnitId', null)" class="text-white underline opacity-50 hover:opacity-100">حذف فیلتر واحد</button>
            </div>
            @endif
        </div>

        {{-- جدول (مشابه جدول قبلی با کمی تغییر در نمایش ستون واحد) --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="p-4">تیکت / فرستنده</th>
                        <th class="p-4 text-center">واحد مقصد</th>
                        <th class="p-4 text-center">وضعیت</th>
                        <th class="p-4">موضوع</th>
                        <th class="p-4 text-left">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm">
                            <span class="font-mono text-indigo-600 block text-[10px]">#{{ $ticket->ticket_code }}</span>
                            <span class="font-bold text-gray-700">{{ $ticket->user?->full_name }}</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded-lg text-gray-600">{{ $ticket->unit?->name }}</span>
                        </td>
                        <td class="p-4 text-center">
                            {{-- لیبل‌های وضعیت مشابه قبل --}}
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $ticket->status === 'accepted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100' }}">
                                {{ $ticket->status_name }}
                            </span>
                        </td>
                        <td class="p-4 text-sm font-medium text-gray-800">{{ $ticket->subject }}</td>
                        <td class="p-4 text-left">
                            <button wire:click="showTicket({{ $ticket->id }})" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">مشاهده کامل</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400 italic text-sm">هیچ تیکتی با این فیلتر یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $tickets->links() }}</div>
        </div>
    </div>
    {{-- مودال جزئیات و ارجاع --}}
    @if($showingTicket)
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">


            {{-- هدر مودال --}}
            <div class="p-6 border-b flex justify-between items-center bg-gray-50 rounded-t-2xl sticky top-0 z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $showingTicket->subject }}</h3>
                    <span class="text-xs text-gray-500 font-mono">#{{ $showingTicket->ticket_code }}</span>
                </div>

            </div>

            <div class="p-6 space-y-6 text-right" dir="rtl">
                {{-- متن تیکت --}}
                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                    <h4 class="text-sm font-bold text-indigo-900 mb-2">متن تیکت:</h4>
                    <p class="text-gray-700 leading-relaxed text-sm">{{ $showingTicket->content }}</p>
                </div>

                {{-- پیوست‌ها --}}
                @if($showingTicket->attachments->count() > 0)
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        مستندات و پیوست‌ها ({{ $showingTicket->attachments->count() }})
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($showingTicket->attachments as $file)
                        <div class="flex items-center p-3 border border-gray-100 rounded-xl bg-gray-50">
                            <span class="text-xs truncate flex-1">{{ $file->name }}</span>
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">دانلود</a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- تاریخچه فعالیت‌ها --}}
                <div class="relative space-y-4 before:absolute before:right-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                    <h4 class="text-sm font-bold text-gray-800 pr-6">تاریخچه فعالیت‌ها:</h4>
                    @foreach($showingTicket->activities->sortByDesc('created_at') as $activity)
                    <div class="relative pr-8">
                        <div class="absolute right-0 top-1 w-5 h-5 rounded-full bg-white border-2 border-indigo-500 flex items-center justify-center z-10 text-[10px]">📨</div>
                        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm text-xs">
                            <div class="flex justify-between mb-1">
                                <span class="font-bold">{{ $activity->user->full_name ?? 'کاربر سیستم' }}</span>
                                <span class="text-gray-400">{{ jdate($activity->created_at)->format('H:i - Y/m/d') }}</span>
                            </div>
                            <p class="text-gray-600">{{ $activity->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="my-6">


            </div>

            <div class="p-4 border-t bg-gray-50 flex justify-end">
                <button wire:click="closeDetail" class="bg-white border border-gray-200 text-gray-600 px-6 py-2 rounded-xl text-sm font-medium hover:bg-gray-100 transition">
                    بستن صفحه
                </button>
            </div>
        </div>
    </div>
    @endif

</div>