<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800"> صندوق تیکت ها</h2>
            <input type="text" wire:model.live="search" placeholder="جستجوی کد یا موضوع..." class="border-gray-200 rounded-xl text-sm w-64">

            <div class="flex items-center gap-2" wire:ignore>
                <input data-jdp id="filter_date_from"
                    onclick="jalaliDatepicker.show(this)"
                    placeholder="از تاریخ"
                    class="border-gray-200 rounded-xl text-[10px] w-28 p-2 cursor-pointer" readonly>

                <input data-jdp id="filter_date_to"
                    onclick="jalaliDatepicker.show(this)"
                    placeholder="تا تاریخ"
                    class="border-gray-200 rounded-xl text-[10px] w-28 p-2 cursor-pointer" readonly>
            </div>
        </div>
        @script
        <script>
            // استفاده از ایونت خود لایووایر برای اطمینان از لود شدن DOM
            $wire.on('init-picker', () => {
                jalaliDatepicker.startWatch();
            });

            const initJdp = () => {
                jalaliDatepicker.startWatch();

                const fromInput = document.getElementById('filter_date_from');
                const toInput = document.getElementById('filter_date_to');

                if (fromInput) {
                    fromInput.addEventListener('jdp:change', e => {
                        $wire.set('dateFrom', e.target.value);
                    });
                }
                if (toInput) {
                    toInput.addEventListener('jdp:change', e => {
                        $wire.set('dateTo', e.target.value);
                    });
                }
            };

            // اجرا در حالت عادی
            initJdp();

            // اجرا برای جابجایی بین صفحات با wire:navigate
            document.addEventListener('livewire:navigated', initJdp);
        </script>
        @endscript
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            <div class="p-6 " dir="rtl">
                {{-- سوئیچ اصلی جهت تیکت --}}
                <div class="flex bg-gray-200 p-1 rounded-2xl w-fit mb-8 shadow-inner">
                    <button wire:click="$set('viewMode', 'received'); $set('statusFilter', 'pending')"
                        class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all {{ $viewMode === 'received' ? 'bg-white shadow-lg text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                        ورودی‌های واحد
                    </button>
                    <button wire:click="$set('viewMode', 'sent'); $set('statusFilter', 'pending')"
                        class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all {{ $viewMode === 'sent' ? 'bg-white shadow-lg text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                        ارسالی‌های من
                    </button>
                </div>

                {{-- تب‌های وضعیت داینامیک --}}
                <div class="flex flex-wrap gap-3 mb-6">
                    <button wire:click="$set('statusFilter', 'all')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'all' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-500 border-gray-200' }}">همه</button>

                    @if($viewMode === 'received')
                    {{-- تب‌های بخش دریافتی --}}
                    <button wire:click="$set('statusFilter', 'pending')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-500' }}">در انتظار بررسی</button>
                    <button wire:click="$set('statusFilter', 'accepted')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'accepted' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-500' }}">قبول شده / در حال انجام</button>
                    <button wire:click="$set('statusFilter', 'rejected')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'rejected' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-500' }}">رد شده (توسط واحد ما)</button>
                    <button wire:click="$set('statusFilter', 'completed')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'completed' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-500' }}">انجام شده</button>
                    @else
                    {{-- تب‌های بخش ارسالی --}}
                    <button wire:click="$set('statusFilter', 'pending')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-500' }}">منتظر تایید مقصد</button>
                    <button wire:click="$set('statusFilter', 'accepted')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'accepted' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-500' }}">تایید شده توسط مقصد</button>
                    <button wire:click="$set('statusFilter', 'rejected')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'rejected' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-500' }}">رد شده توسط مقصد</button>
                    <button wire:click="$set('statusFilter', 'completed')" class="px-5 py-1.5 rounded-full border text-xs font-bold {{ $statusFilter === 'completed' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-500' }}">تکمیل و نهایی شده</button>
                    @endif
                </div>

                {{-- ادامه کد جدول تیکت‌ها که قبلا داشتید --}}
            </div>
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="p-4 text-right">کد / فرستنده</th>
                        <th class="p-4 text-center">اولویت</th>
                        <th class="p-4 text-center">وضعیت</th>
                        <th class="p-4 text-center">زمان انتظار</th>
                        <th class="p-4 text-right">موضوع</th>
                        <th class="p-4 text-left">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <span class="font-mono text-indigo-600 block text-xs">#{{ $ticket->ticket_code }}</span>
                            <span class="text-sm font-bold text-gray-700">{{ $ticket->user?->full_name ?? 'کاربر سیستم' }}</span>
                        </td>

                        {{-- اولویت --}}
                        <td class="p-4 text-center">
                            @php
                            $priorityColors = [
                            'urgent' => 'bg-red-100 text-red-700 border-red-200',
                            'normal' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'low' => 'bg-gray-100 text-gray-700 border-gray-200',
                            ];
                            $priorityLabels = ['urgent' => 'فوری', 'normal' => 'معمولی', 'low' => 'کم‌اهمیت'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $priorityColors[$ticket->priority] ?? $priorityColors['low'] }}">
                                {{ $priorityLabels[$ticket->priority] ?? 'نامشخص' }}
                            </span>
                        </td>

                        {{-- وضعیت با رنگ‌بندی اختصاصی --}}
                        <td class="p-4 text-center">
                            @php
                            $statusColors = [
                            'created' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'forwarded' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'accepted' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'closed' => 'bg-gray-100 text-gray-700 border-gray-200',
                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                            ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $statusColors[$ticket->status] ?? 'bg-gray-100' }}">
                                {{ $ticket->status_name }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $ticket->waiting_duration['class'] }}">
                                {{ $ticket->waiting_duration['text'] }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="text-sm font-bold text-gray-800">{{ $ticket->subject }}</div>
                        </td>

                        <td class="p-4 text-left flex items-center justify-end gap-2">
                            {{-- نمایش نام مسئول (تست رابطه assignee) --}}
                            @if($ticket->current_assignee_id)
                            <span class="text-[10px] bg-gray-50 text-gray-500 px-2 py-1 rounded border">
                                در کارتابل: <b>{{ $ticket->assignee?->full_name ?? 'خطا در رابطه' }}</b>
                            </span>
                            @endif
                            @if($currentTab === 'pending')


                            {{-- دکمه تایید و رد فقط برای تیکت‌های پذیرفته نشده --}}
                            @if($ticket->status !== 'accepted' && $ticket->status !== 'rejected')
                            <button wire:click="acceptTicket({{ $ticket->id }})" class="bg-green-50 text-green-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-100">
                                تایید
                            </button>

                            <button onclick="confirm('آیا از رد تیکت اطمینان دارید؟') || event.stopImmediatePropagation()"
                                wire:click="rejectTicket({{ $ticket->id }})"
                                class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100">
                                رد
                            </button>
                            @endif
                            @else
                            <button wire:click="showTicket({{ $ticket->id }})" class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-100">
                                مشاهده و ارجاع
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400">تیکتی یافت نشد.</td>
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

            {{-- بخش فرم ارجاع (داخل مودال) --}}
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                <button wire:click="closeDetail" class="text-gray-400 hover:text-red-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    ارجاع به واحد دیگر
                </h4>

                <div class="relative mb-4">
                    <input type="text" wire:model.live="unitSearch"
                        class="w-full border-gray-200 rounded-lg p-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="جستجوی نام واحد مقصد...">

                    @if(!empty($units))
                    <div class="absolute border z-50 w-full bg-white shadow-xl rounded-lg mt-1 overflow-hidden">
                        @foreach($units as $unit)
                        <button wire:click="selectTargetUnit({{ $unit->id }}, '{{ $unit->name }}')"
                            class="w-full text-right px-4 py-2.5 hover:bg-indigo-50 text-sm border-b last:border-0">
                            {{ $unit->name }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                @if($targetUnitId)
                <div class="mb-4 p-3 bg-indigo-100 text-indigo-800 rounded-lg text-xs flex justify-between items-center">
                    <span>واحد انتخاب شده: <b>{{ $targetUnitName }}</b></span>
                    <button wire:click="$set('targetUnitId', null)" class="text-red-500 underline">تغییر</button>
                </div>
                @endif

                <textarea wire:model="forwardNote"
                    class="w-full border-gray-200 rounded-lg p-3 text-sm focus:ring-indigo-500"
                    rows="2"
                    placeholder="توضیحات یا علت ارجاع (اختیاری)"></textarea>

                <button wire:click="forward"
                    class="mt-3 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all">
                    ارجاع تیکت
                </button>
            </div>
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