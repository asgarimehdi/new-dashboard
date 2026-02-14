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
                        <th class="p-4 text-right">کد / ایجاد کننده</th>
                        <th class="p-4 text-center">اولویت</th>
                        <th class="p-4 text-center">وضعیت</th>
                        <th class="p-4 text-center">زمان انتظار</th>
                        <th class="p-4 text-right">موضوع</th>
                        <th class="p-4 text-center">در انتظار تایید...</th>
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
                            'completed' => 'bg-gray-100 text-gray-700 border-gray-200',
                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                            ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $statusColors[$ticket->status] ?? 'bg-gray-100' }}">
                                {{ $ticket->status_name }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if(in_array($ticket->status, ['completed']))
                            <span class="text-xs text-gray-400">-----</span>
                            @elseif(in_array($ticket->status, ['rejected']))
                            <span class="text-xs text-gray-400">-----</span>
                            @else
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $ticket->waiting_duration['class'] }}">
                                {{ $ticket->waiting_duration['text'] }}
                            </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="text-sm font-bold text-gray-800">{{ $ticket->subject }}</div>
                        </td>
                        <td class="p-4 text-center">
                            @if(in_array($ticket->status, ['created', 'forwarded']))
                            <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                                {{ $ticket->unit?->name ?? '---' }}
                            </span>
                            @elseif(in_array($ticket->status, ['rejected']))
                            <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                                <span class="text-xs text-gray-400">رد شده</span>
                            </span>
                            @elseif(in_array($ticket->status, ['completed']))
                            <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                                <span class="text-xs text-gray-400">بسته شده</span>
                            </span>
                            @else
                            <span class="text-xs text-gray-400">تایید شده</span>
                            @endif
                        </td>
                        <td class="p-4 text-left flex items-center justify-end gap-2">
                            {{-- نمایش نام مسئول (تست رابطه assignee) --}}
                            @if($ticket->current_assignee_id)
                            <span class="text-[10px] bg-gray-50 text-gray-500 px-2 py-1 rounded border">
                                در کارتابل: <b>{{ $ticket->assignee?->full_name ?? 'خطا در رابطه' }}</b>
                            </span>
                            @endif



                            {{-- دکمه تایید و رد فقط برای تیکت‌های پذیرفته نشده --}}
                            @if($ticket->status !== 'accepted' &&
                            $ticket->status !== 'rejected'&&
                            $ticket->status !== 'completed')
                            <button wire:click="acceptTicket({{ $ticket->id }})" class="bg-green-50 text-green-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-100">
                                تایید
                            </button>

                            <button onclick="confirm('آیا از رد تیکت اطمینان دارید؟') || event.stopImmediatePropagation()"
                                wire:click="rejectTicket({{ $ticket->id }})"
                                class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100">
                                رد
                            </button>

                            @endif
                            <button wire:click="showTicket({{ $ticket->id }})" class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-100">
                                تاریخچه
                            </button>

                            {{-- دکمه تکمیل: فقط اگر وضعیت accepted باشد --}}
                            @if($ticket->status === 'accepted')
                            <button wire:click="openCompletionModal({{ $ticket->id }})"
                                class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-xl text-sm font-bold transition-all">
                                ارجاع یا بستن 
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
 {{-- مودال ۲: عملیات (ارجاع / بستن) --}}
@if($isCompletionModalOpen)
<input type="hidden" wire:model="showingTicketId">
<div class="fixed inset-0 bg-gray-900/70 backdrop-blur-md z-[70] flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/50">
        
        {{-- هدر داینامیک --}}
        <div class="p-6 text-center border-b relative {{ $targetUnitId ? 'bg-indigo-50' : 'bg-emerald-50' }} transition-colors">
            <h3 class="text-lg font-black {{ $targetUnitId ? 'text-indigo-800' : 'text-emerald-800' }}">
                {{ $targetUnitId ? '🚀 عملیات ارجاع تیکت' : '✅ اعلام اتمام فعالیت' }}
            </h3>
            <p class="text-[11px] mt-1 text-gray-500">لطفاً مستندات و گزارش نهایی را وارد نمایید</p>
            <button wire:click="$set('isCompletionModalOpen', false)" class="absolute top-4 left-4 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        </div>

        <div class="p-8 space-y-6" dir="rtl">
            
            {{-- فیلد جستجوی واحد مقصد --}}
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600 mr-2">ارجاع به واحد دیگر (اختیاری):</label>
                <div class="relative group">
                    <input type="text" wire:model.live="unitSearch" 
                           class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-100 transition-all" 
                           placeholder="نام واحد مقصد را تایپ کنید...">
                    
                    @if(!empty($units))
                    <div class="absolute z-[80] w-full bg-white shadow-2xl rounded-2xl mt-2 border border-gray-100 max-h-48 overflow-y-auto overflow-x-hidden p-2">
                        @foreach($units as $u)
                        <button wire:click="selectTargetUnit({{ $u->id }}, '{{ $u->name }}')" 
                                class="w-full text-right px-4 py-3 hover:bg-indigo-50 rounded-xl text-xs transition-colors mb-1 last:mb-0 border-b border-gray-50 last:border-0 font-bold text-gray-700">
                            {{ $u->name }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                @if($targetUnitId)
                <div class="flex items-center justify-between bg-indigo-600 text-white px-4 py-2 rounded-xl mt-2 animate-bounce-short">
                    <span class="text-xs font-bold italic">ارسال به: {{ $targetUnitName }}</span>
                    <button wire:click="$set('targetUnitId', null)" class="text-[10px] bg-white/20 px-2 py-1 rounded-lg hover:bg-white/40">لغو ارجاع</button>
                </div>
                @endif
            </div>

            {{-- فیلد توضیحات --}}
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600 mr-2">گزارش شما (اجباری):</label>
                <textarea wire:model="completionNote" 
                          class="w-full bg-gray-50 border-gray-200 rounded-2xl p-4 text-sm focus:bg-white focus:ring-4 focus:ring-emerald-100 transition-all" 
                          rows="3" placeholder="توضیحات لازم جهت بستن یا ارجاع..."></textarea>
                @error('completionNote') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
            </div>

            {{-- بخش آپلود فایل - بازطراحی شده شبیه صفحه ایجاد تیکت --}}
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600 mr-2">مستندات پیوست:</label>
                <div class="relative group cursor-pointer">
                    <input type="file" wire:model="completionFiles" multiple 
                           class="absolute inset-0 opacity-0 cursor-pointer z-10">
                    <div class="border-2 border-dashed border-gray-200 group-hover:border-emerald-400 group-hover:bg-emerald-50 rounded-2xl p-6 transition-all flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-gray-100 group-hover:bg-emerald-100 flex items-center justify-center transition-colors">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-500 group-hover:text-emerald-700">انتخاب فایل‌ها یا کشیدن به اینجا</span>
                        <div wire:loading wire:target="completionFiles" class="text-[10px] text-blue-600 font-bold animate-pulse">در حال آپلود...</div>
                    </div>
                </div>
                {{-- پیش‌نمایش فایل‌های انتخاب شده --}}
                @if($completionFiles)
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($completionFiles as $index => $file)
                    <div class="flex items-center gap-2 bg-gray-100 px-2 py-1 rounded-lg border text-[10px] text-gray-600">
                        <span class="truncate max-w-[100px]">{{ $file->getClientOriginalName() }}</span>
                        <button type="button" wire:click="removeFile({{ $index }})" class="text-red-500 font-bold">×</button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- دکمه نهایی داینامیک --}}
            <button wire:click="submitAction" 
                    class="w-full py-4 rounded-2xl text-sm font-black text-white shadow-xl transition-all hover:scale-[1.02] active:scale-95
                    {{ $targetUnitId ? 'bg-indigo-600 shadow-indigo-200 hover:bg-indigo-700' : 'bg-emerald-600 shadow-emerald-200 hover:bg-emerald-700' }}">
                {{ $targetUnitId ? 'تایید و ارجاع تیکت به واحد مقصد' : 'ثبت گزارش و مختومه کردن تیکت' }}
            </button>
        </div>
    </div>
</div>
@endif
    {{-- مودال جزئیات --}}
    {{-- مودال ۱: مشاهده جزئیات و تاریخچه (فقط خواندنی) --}}
@if($showingTicket)
<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden border border-white/20">
        {{-- هدر --}}
        <div class="p-6 border-b flex justify-between items-center bg-gray-50/50 sticky top-0 z-10">
            <div class="text-right" dir="rtl">
                <h3 class="text-lg font-extrabold text-gray-800">{{ $showingTicket->subject }}</h3>
                <span class="text-xs text-indigo-500 font-mono bg-indigo-50 px-2 py-0.5 rounded-full">#{{ $showingTicket->ticket_code }}</span>
            </div>
            <button wire:click="closeDetail" class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all text-2xl">&times;</button>
        </div>

        {{-- محتوا --}}
        <div class="p-6 overflow-y-auto space-y-8 text-right custom-scrollbar" dir="rtl">
            {{-- متن اصلی تیکت --}}
            <div class="relative p-5 bg-gradient-to-br from-gray-50 to-indigo-50/30 rounded-2xl border border-indigo-100/50 shadow-sm">
                <div class="absolute -top-3 right-4 px-3 py-1 bg-indigo-600 text-white text-[10px] rounded-full shadow-lg">شرح درخواست</div>
                <p class="text-gray-700 leading-relaxed text-sm pt-2">{{ $showingTicket->content }}</p>
            </div>

            {{-- ضمیمه‌ها --}}
            @if($showingTicket->attachments->count() > 0)
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2 pr-2 border-r-4 border-indigo-500">فایل‌های پیوست</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($showingTicket->attachments as $file)
                    <div class="group flex items-center p-3 border border-gray-100 rounded-2xl bg-white hover:border-indigo-300 hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="text-xs truncate flex-1 px-3 text-gray-600">{{ $file->name }}</span>
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-xs font-bold text-indigo-600 px-3 py-1 bg-indigo-50 rounded-lg hover:bg-indigo-100">دریافت</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- تاریخچه --}}
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-6 flex items-center gap-2 pr-2 border-r-4 border-amber-500">تاریخچه و پیگیری‌ها</h4>
                <div class="relative space-y-6 before:absolute before:right-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gradient-to-b before:from-indigo-500 before:to-gray-100">
                    @foreach($showingTicket->activities->sortByDesc('created_at') as $activity)
                    <div class="relative pr-8">
                        <div class="absolute right-0 top-1 w-6 h-6 rounded-full bg-white border-2 border-indigo-500 flex items-center justify-center z-10 shadow-sm transition-transform hover:scale-125">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                        </div>
                        <div class="bg-gray-50/80 backdrop-blur-sm p-4 rounded-2xl border border-gray-100 shadow-sm group hover:bg-white hover:border-indigo-200 transition-all">
                            <div class="flex justify-between items-center mb-2 text-[11px]">
                                <span class="font-black text-gray-800 bg-white px-2 py-1 rounded-lg shadow-sm border">{{ $activity->user->full_name }}</span>
                                <span class="text-gray-400 font-mono">{{ jdate($activity->created_at)->format('H:i - Y/m/d') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 leading-6">{{ $activity->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-5 border-t bg-white flex justify-end">
            <button wire:click="closeDetail" class="bg-gray-800 text-white px-8 py-2.5 rounded-2xl text-sm font-bold hover:bg-black transition-all shadow-lg shadow-gray-200">فهمیدم</button>
        </div>
    </div>
</div>
@endif
</div>