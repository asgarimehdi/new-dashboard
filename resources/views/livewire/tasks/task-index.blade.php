<div class="p-4 max-w-6xl mx-auto space-y-6">

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-black text-gray-800 mb-6 flex items-center gap-2">
            <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
            مدیریت تسک‌ها
        </h1>

        {{-- نمایش پیام موفقیت --}}
        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex justify-between items-center animate-bounce">
                <span class="font-bold">{{ session('success') }}</span>
                <button @click="open = false" class="text-green-500">&times;</button>
            </div>
        @endif

        {{-- فرم ثبت و ویرایش --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-10">
            <div class="{{ $taskId ? 'bg-amber-500' : 'bg-indigo-600' }} p-4 text-white flex justify-between items-center transition-colors">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ $taskId ? 'ویرایش تسک شماره ' . $taskId : 'ایجاد تسک عملیاتی جدید' }}
                </h3>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- ستون اطلاعات اصلی --}}
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">عنوان تسک <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="title" placeholder="چه کاری باید انجام شود؟" 
                                   class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all">
                            @error('title') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات و جزئیات</label>
                            <textarea wire:model="description" rows="4" placeholder="توضیحات لازم برای مجری تسک..." 
                                      class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"></textarea>
                        </div>

                        {{-- بخش آپلود فایل فشرده --}}
                        <div class="bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200" 
                             x-data="{ isUploading: false, progress: 0 }" 
                             x-on:livewire-upload-start="isUploading = true" 
                             x-on:livewire-upload-finish="isUploading = false" 
                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                            
                            <label class="flex flex-col items-center cursor-pointer">
                                <svg class="w-10 h-10 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                <span class="text-sm font-bold text-gray-600">انتخاب فایل‌های ضمیمه</span>
                                <input type="file" wire:model="files" multiple class="hidden">
                            </label>

                            <div x-show="isUploading" class="mt-4 w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-full transition-all" :style="`width: ${progress}%` text-align: center;"></div>
                            </div>

                            @if($files)
                                <div class="mt-4 grid grid-cols-2 gap-2">
                                    @foreach($files as $index => $file)
                                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-100 shadow-sm text-xs">
                                            <span class="truncate text-indigo-600 font-bold">{{ $file->getClientOriginalName() }}</span>
                                            <button wire:click="removeFile({{ $index }})" class="text-red-500">×</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ستون تنظیمات جانبی --}}
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-5 rounded-2xl space-y-5">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2 italic">واحد هدف</label>
                                <select wire:model="unit_id" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm shadow-sm outline-none">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach($units as $unit) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2 italic">اولویت اجرا</label>
                                <select wire:model="priority" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm shadow-sm outline-none font-bold {{ $priority == 'urgent' ? 'text-red-600' : 'text-indigo-600' }}">
                                    <option value="low">کم (عادی)</option>
                                    <option value="normal">معمولی</option>
                                    <option value="urgent">فوری / بحرانی</option>
                                </select>
                            </div>

                            <div wire:ignore>
                                <label class="block text-xs font-black text-gray-400 uppercase mb-2 italic">مهلت انجام</label>
                                <input data-jdp id="due_date_picker" type="text" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm shadow-sm outline-none" placeholder="۱۴۰۴/--/--">
                            </div>

                            {{-- ارجاع مستقیم در زمان ثبت --}}
                            @if(!$taskId)
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="block text-xs font-bold text-indigo-600 mb-2">ارجاع همزمان به شخص:</label>
                                    <input type="text" wire:model.live.debounce.300ms="assign_user_search" class="w-full border-gray-200 rounded-xl px-3 py-2 text-xs mb-2 shadow-inner" placeholder="جستجوی نام...">
                                    
                                    @if($assign_user_search && count($assignableUsers) > 0)
                                        <div class="bg-white border rounded-xl shadow-lg max-h-32 overflow-y-auto mb-2">
                                            @foreach($assignableUsers as $user)
                                                <div wire:click="$set('assign_user_id', {{ $user->id }})" class="p-2 hover:bg-indigo-50 cursor-pointer text-[11px] border-b last:border-0">{{ $user->full_name }}</div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($assign_user_id)
                                        <div class="bg-green-100 p-2 rounded-lg flex justify-between items-center text-[10px] text-green-700 font-bold">
                                            <span>✓ آماده ارجاع به: {{ \App\Models\User::find($assign_user_id)->full_name }}</span>
                                            <button wire:click="$set('assign_user_id', null)">&times;</button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- فایل‌های قبلی (در حالت ویرایش) --}}
                        @if($taskId)
                            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                                <label class="text-xs font-bold text-amber-800 mb-2 block italic underline">فایل‌های پیوست فعلی:</label>
                                <div class="space-y-1">
                                    @foreach(\App\Models\Attachment::where('task_id', $this->taskId)->get() as $attach)
                                        <div class="flex justify-between items-center text-[10px] bg-white p-1 rounded border border-amber-200 shadow-sm">
                                            <span class="truncate max-w-[120px]">{{ $attach->file_name }}</span>
                                            <button wire:click="deleteAttachment({{ $attach->id }})" class="text-red-500 font-bold">حذف</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- دکمه‌های عملیاتی --}}
                <div class="mt-8 flex gap-4 border-t pt-6">
                    <button wire:click="save" class="{{ $taskId ? 'bg-amber-500' : 'bg-indigo-600' }} text-white px-10 py-3 rounded-xl font-bold shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        {{ $taskId ? 'اعمال تغییرات نهایی' : 'ثبت و سازماندهی تسک' }}
                    </button>
                    @if($taskId)
                        <button wire:click="cancelEdit" class="bg-gray-100 text-gray-600 px-8 py-3 rounded-xl hover:bg-gray-200 transition-all font-bold">انصراف</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- بخش ارجاع مجدد (پایین فرم - در صورتی که دکمه ارجاع در جدول زده شود) --}}
        @if($assign_task_id)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
                    <div class="bg-indigo-700 p-4 text-white font-bold flex justify-between items-center">
                        <span>ارجاع تسک کد #{{ $assign_task_id }}</span>
                        <button wire:click="$set('assign_task_id', null)" class="text-2xl">&times;</button>
                    </div>
                    <div class="p-6 space-y-4">
                        <input type="text" wire:model.live.debounce.300ms="assign_user_search" 
                               class="w-full border-gray-200 rounded-xl px-4 py-3 shadow-inner outline-none focus:ring-2 focus:ring-indigo-500" 
                               placeholder="جستجوی نام کاربر مقصد...">
                        
                        @if($assign_user_search)
                            <div class="border rounded-xl max-h-48 overflow-y-auto">
                                @forelse($assignableUsers as $user)
                                    <div wire:click="$set('assign_user_id', {{ $user->id }})" 
                                         class="p-3 hover:bg-indigo-50 cursor-pointer text-sm border-b last:border-0 transition-colors flex justify-between items-center">
                                        <span class="font-bold">{{ $user->full_name }}</span>
                                        <span class="text-[10px] text-gray-400 italic">واحد {{ $user->unit?->name }}</span>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-gray-400 text-sm italic">کاربری یافت نشد.</div>
                                @endforelse
                            </div>
                        @endif

                        @if($assign_user_id)
                            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-center">
                                <p class="text-sm text-green-700">تسک به <b>{{ \App\Models\User::find($assign_user_id)->full_name }}</b> منتقل می‌شود.</p>
                            </div>
                        @endif

                        <div class="flex gap-2 pt-4">
                            <button wire:click="assignTask" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 active:scale-95 transition-all">ثبت ارجاع</button>
                            <button wire:click="$set('assign_task_id', null)" class="px-6 py-3 text-gray-500 font-bold">لغو</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- اسکریپت تقویم --}}
<script>
    jalaliDatepicker.startWatch();
    document.getElementById('due_date_picker').addEventListener('jdp:change', e => {
        @this.set('due_date', e.target.value);
    });
    window.addEventListener('set-datepicker', event => {
        const el = document.getElementById('due_date_picker');
        if (el) el.value = event.detail.value;
    });
    window.addEventListener('reset-datepicker', () => {
        const el = document.getElementById('due_date_picker');
        if (el) el.value = '';
    });
</script>
<div class="flex gap-2 mb-4">

    <button
        wire:click="$set('task_view', 'all')"
        class="px-3 py-1 rounded
            {{ $task_view === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}"
    >
        همه تسک‌ها
    </button>

    <button
        wire:click="$set('task_view', 'inbox')"
        class="px-3 py-1 rounded
            {{ $task_view === 'inbox' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}"
    >
        دریافتی
    </button>

    <button
        wire:click="$set('task_view', 'sent')"
        class="px-3 py-1 rounded
            {{ $task_view === 'sent' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}"
    >
        ارسالی
    </button>

</div>



<div class="flex flex-wrap gap-4 bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
    {{-- فیلتر وضعیت --}}
    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-bold text-gray-500 mb-1">وضعیت:</label>
        <select wire:model.live="filter_status" class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">همه وضعیت‌ها</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}">{{ $status->title }}</option>
            @endforeach
        </select>
    </div>

    {{-- فیلتر اولویت --}}
    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-bold text-gray-500 mb-1">اولویت:</label>
        <select wire:model.live="filter_priority" class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">همه اولویت‌ها</option>
            <option value="low">کم</option>
            <option value="normal">معمولی</option>
            <option value="urgent">فوری</option>
        </select>
    </div>

    {{-- دکمه پاکسازی --}}
    <div class="flex items-end pb-1">
<button wire:click="resetFilters()" class="text-xs text-red-500 hover:text-red-700 underline transition">
    حذف فیلترها
</button>
    </div>
        {{-- سرچ --}}
    <input
        type="text"
        wire:model.live.debounce.500ms="search"
        class="w-full border rounded px-3 py-2"
        placeholder="جستجو عنوان یا واحد..."
    >
</div>
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold">{{ $show_trash ? 'زباله‌دان تسک‌ها' : 'لیست تسک‌های فعال' }}</h2>
    <button wire:click="toggleTrash" class="px-4 py-2 rounded-lg text-sm font-bold {{ $show_trash ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
        {{ $show_trash ? 'بازگشت به لیست اصلی' : 'مشاهده زباله‌دان 🗑️' }}
    </button>
</div>

    {{-- جدول --}}
  {{-- جدول مدیریت تسک‌ها --}}
<div class="bg-white rounded shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border-b">عنوان و اولویت</th>
                    <th class="p-3 border-b hidden md:table-cell">واحد مربوطه</th>
                    <th class="p-3 border-b hidden lg:table-cell">تاریخ ثبت</th>
                    <th class="p-3 border-b hidden sm:table-cell">آخرین ارجاع</th>
                    <th class="p-3 border-b text-center">مهلت انجام</th>
                    <th class="p-3 border-b">وضعیت</th>
                    <th class="p-3 border-b text-center">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    {{-- ردیف اصلی: اگر مهلت کم باشد قرمز ملایم و اگر منقضی شده باشد خاکستری می‌شود --}}
                    <tr class="border-t hover:bg-gray-50 transition-colors {{ $task->deadline_status == 'urgent' ? 'bg-red-50' : ($task->deadline_status == 'expired' ? 'bg-gray-100' : '') }}">
                        
                        {{-- عنوان و نشان اولویت --}}
                        <td class="p-3">
                            
                            <div class="font-bold text-gray-800">{{ $task->title }}</div>
                            <div class="mt-1">
                                @php
        $priorityColors = [
            'urgent' => 'bg-red-100 text-red-700 border-red-200',
            'normal' => 'bg-blue-100 text-blue-700 border-blue-200',
            'low'    => 'bg-gray-100 text-gray-600 border-gray-200',
        ];
        $priorityLabels = [
            'urgent' => 'فوری',
            'normal' => 'معمولی',
            'low'    => 'کم',
        ];
    @endphp
    <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $priorityColors[$task->priority] ?? $priorityColors['low'] }}">
                                    {{ $priorityLabels[$task->priority] ?? 'معمولی' }}
                                </span>
                            </div>
                            {{-- نمایش واحد در موبایل (چون ستون واحد در موبایل مخفی است) --}}
                            <div class="md:hidden text-[11px] text-gray-500 mt-1">
                                واحد: {{ $task->unit->name }}
                            </div>
                        </td>

                        {{-- واحد (فقط در تبلت و دسکتاپ) --}}
                        <td class="p-3 text-sm hidden md:table-cell">
                            {{ $task->unit->name }}
                        </td>

                        {{-- تاریخ ثبت شمسی (فقط در دسکتاپ) --}}
                        <td class="p-3 text-sm text-gray-600 hidden lg:table-cell">
                            {{ $task->shamsi_created }}
                        </td>

                        {{-- آخرین ارجاع (در موبایل مخفی) --}}
                        <td class="p-3 text-sm text-gray-600 hidden sm:table-cell text-center">
                            @if($task->lastAssignment)
                                {{ \Hekmatinasser\Verta\Verta::instance($task->lastAssignment->created_at)->format('Y/m/d') }}
                            @else
                                <span class="text-gray-400">---</span>
                            @endif
                        </td>

                        {{-- مهلت انجام (با افکت چشمک‌زن برای موارد فوری) --}}
                        <td class="p-3 text-sm font-bold text-center {{ $task->deadline_status == 'urgent' ? 'text-red-600 animate-pulse' : '' }}">
                            {{ $task->shamsi_due_date }}
                            @if($task->deadline_status == 'expired')
                                <div class="text-[10px] font-normal text-red-400">(منقضی شده)</div>
                            @endif
                        </td>

                        {{-- تغییر وضعیت --}}
<td class="p-3 text-center">
    @php
        $idMap = [
            'new'         => 1, // جدید
            'assigned'    => 2, // ارجاع شده
            'in_progress' => 3, // در حال انجام
            'completed'   => 4, // انجام شده
            'closed'      => 5, // بسته شده
        ];
        
        $currentId = (int) $task->task_status_id;
        $isCreator = (int) $task->created_by === 1; // دستی
        
        $lastAssignment = $task->assignments->last();
        $isAssignee = $lastAssignment && (int) $lastAssignment->to_user_id === 1; // دستی

        $statusColors = [
            $idMap['new']         => 'bg-blue-100 text-blue-700',
            $idMap['assigned']    => 'bg-indigo-100 text-indigo-700',
            $idMap['in_progress'] => 'bg-yellow-100 text-yellow-700',
            $idMap['completed']   => 'bg-green-100 text-green-700',
            $idMap['closed']      => 'bg-gray-700 text-white',
        ];
    @endphp

    <div class="flex flex-col items-center gap-2 text-right" dir="rtl">
        <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $statusColors[$currentId] ?? 'bg-gray-100' }}">
            {{ $task->status->title ?? 'بدون وضعیت' }}
        </span>

        <div class="flex flex-wrap gap-1 justify-center">
            
            {{-- ۱. دکمه ارجاع: نمایش برای مدیر یا کسی که تسک فعلاً دست اوست --}}
            @if(($isCreator || $isAssignee) && $currentId !== $idMap['closed'])
                <button wire:click="$set('assign_task_id', {{ $task->id }})" 
                        class="text-[9px] bg-indigo-600 text-white px-1.5 py-0.5 rounded shadow hover:bg-indigo-700">
                    ارجاع
                </button>
            @endif

            {{-- ۲. دسترسی‌های گیرنده تسک --}}
            @if($isAssignee && $currentId !== $idMap['closed'])
                @if($currentId !== $idMap['in_progress'])
                    <button wire:click="changeStatus({{ $task->id }}, {{ $idMap['in_progress'] }})" class="text-[9px] bg-yellow-500 text-white px-1.5 py-0.5 rounded">شروع کار</button>
                @endif
                @if($currentId !== $idMap['completed'])
                    <button wire:click="changeStatus({{ $task->id }}, {{ $idMap['completed'] }})" class="text-[9px] bg-green-600 text-white px-1.5 py-0.5 rounded">اتمام کار</button>
                @endif
            @endif

            {{-- ۳. دسترسی‌های مدیر (ایجادکننده) --}}
            @if($isCreator)
                {{-- دکمه بستن/لغو: مدیر همیشه می‌تواند ببندد مگر اینکه قبلاً بسته شده باشد --}}
                @if($currentId !== $idMap['closed'])
                    <button wire:click="changeStatus({{ $task->id }}, {{ $idMap['closed'] }})" 
                            class="text-[9px] {{ $currentId === $idMap['completed'] ? 'bg-gray-800' : 'bg-red-700' }} text-white px-1.5 py-0.5 rounded shadow">
                        {{ $currentId === $idMap['completed'] ? 'تایید و بستن' : 'لغو/بستن تسک' }}
                    </button>
                @endif
                
                {{-- بازگشایی تسک بسته شده --}}
                @if($currentId === $idMap['closed'])
                    <button wire:click="changeStatus({{ $task->id }}, {{ $idMap['new'] }})" class="text-[9px] bg-blue-600 text-white px-1.5 py-0.5 rounded">بازگشایی</button>
                @endif
            @endif
        </div>
    </div>
</td>

                        {{-- دکمه‌های عملیاتی --}}
                  <td class="p-3 text-center">
    <div class="flex items-center justify-center gap-2">
        @if($show_trash)
        {{-- دکمه بازیابی --}}
        <button wire:click="restoreTask({{ $task->id }})" class="text-green-600" title="بازیابی">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>

        {{-- دکمه حذف دائمی --}}
        <button wire:confirm="آیا مطمئن هستید؟ این تسک برای همیشه پاک خواهد شد و قابل بازیابی نیست." 
                wire:click="forceDeleteTask({{ $task->id }})" class="text-red-800" title="حذف دائمی">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    @else
        
        {{-- دکمه ارجاع --}}
        <button 
            wire:click="$set('assign_task_id', {{ $task->id }})" 
            class="flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-100 transition shadow-sm"
            title="ارجاع به واحد دیگر"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span class="text-xs font-bold hidden xl:inline">ارجاع</span>
        </button>

        {{-- دکمه ویرایش --}}
        <button 
            wire:click="edit({{ $task->id }})" 
            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"
            title="ویرایش"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </button>

        {{-- دکمه حذف --}}
        <button 
            onclick="confirm('آیا از حذف این تسک مطمئن هستید؟') || event.stopImmediatePropagation()" 
            wire:click="delete({{ $task->id }})" 
            class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
            title="حذف"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>

        {{-- دکمه تاریخچه --}}
        <button 
            wire:click="$set('open_activity_task_id', {{ $open_activity_task_id === $task->id ? 'null' : $task->id }})" 
            class="p-1.5 text-gray-500 hover:bg-gray-100 rounded transition"
            title="تاریخچه"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
        <button wire:click="toggleAttachments({{ $task->id }})" class="text-orange-600 hover:text-orange-900" title="مشاهده پیوست‌ها">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
    </svg>
</button>
        @endif
    </div>
</td>
                    </tr>

                    {{-- بخش نمایش تاریخچه (بصورت کشویی زیر هر ردیف) --}}
                    @if($open_activity_task_id === $task->id)
                        <tr>
                            <td colspan="7" class="bg-gray-50 p-4 border-b shadow-inner">
                                <div class="max-w-3xl mx-auto">
                                    <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                                        <span>تاریخچه و فعالیت‌های تسک</span>
                                    </h4>
                                   <ul class="relative border-r-2 border-gray-200 pr-4 space-y-4">
    @foreach($task->activities->sortByDesc('created_at') as $activity)
        <li class="relative">
            {{-- نقطه روی خط زمان --}}
            <div class="absolute -right-[21px] mt-1.5 h-3 w-3 rounded-full {{ $activity->action === 'حذف فایل' ? 'bg-red-400' : ($activity->action === 'آپلود فایل' ? 'bg-green-400' : 'bg-gray-300') }} border-2 border-white"></div>
            
            <div class="text-xs text-gray-500 font-mono">
                {{ \Hekmatinasser\Verta\Verta::instance($activity->created_at)->format('Y/m/d H:i') }}
            </div>

            <div class="text-sm">
                <span class="font-semibold text-gray-700">
                    {{ match($activity->action) {
                        'assign' => 'ارجاع تسک',
                        'status_change' => 'تغییر وضعیت',
                        'آپلود فایل' => '📎 آپلود فایل',
                        'حذف فایل' => '🗑️ حذف فایل',
                       'ثبت کامنت' => '💬ثبت نظر جدید',
                        default => $activity->action,
                    } }}:
                </span>

                {{-- منطق نمایش جزئیات بر اساس نوع اکشن --}}
                @if($activity->action === 'assign')
                    @php
                        $assignment = $task->assignments->where('created_at', '<=', $activity->created_at)->sortByDesc('created_at')->first();
                    @endphp
                    @if($assignment)
                        <span class="text-indigo-600 text-xs">از {{ $assignment->fromUser?->full_name ?? 'سیستم' }} به {{ $assignment->toUser?->full_name }}</span>
                    @endif
                @elseif($activity->action === 'status_change' && $activity->oldStatus && $activity->newStatus)
                    <span class="text-gray-600 italic text-xs">از "{{ $activity->oldStatus->title }}" به "{{ $activity->newStatus->title }}"</span>
                @else
                    {{-- نمایش توضیحات برای فایل‌ها و موارد پیش‌فرض --}}
                    <span class="text-gray-600 text-xs">{{ $activity->description }}</span>
                @endif
            </div>

            <div class="text-[10px] text-gray-400 italic">توسط: {{ $activity->user?->full_name ?? 'سیستم' }}</div>
        </li>
    @endforeach
</ul>                               </div>
<div class="mt-8 border-t pt-6">
    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        نظرات و گفتگو
    </h3>

    <div class="mb-6">
        <textarea wire:model="commentContent" 
                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                  rows="3" 
                  placeholder="نظری بنویسید..."></textarea>
        <div class="flex justify-end mt-2">
            <button wire:click="addComment({{ $task->id }})" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs hover:bg-indigo-700 transition">
                ثبت نظر
            </button>
        </div>
    </div>

    <div class="space-y-4 max-h-60 overflow-y-auto p-2">
        @forelse($task->comments as $comment)
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 relative">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[11px] font-bold text-indigo-700">
                        {{ $comment->user?->full_name ?? 'کاربر مهمان' }}
                    </span>
                    <span class="text-[10px] text-gray-400 font-mono">
                        {{ \Hekmatinasser\Verta\Verta::instance($comment->created_at)->format('Y/m/d H:i') }}
                    </span>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ $comment->content }}
                </p>
            </div>
        @empty
            <p class="text-center text-gray-400 text-xs italic">هنوز نظری ثبت نشده است.</p>
        @endforelse
    </div>
</div>
                            </td>
                        </tr>
                    @endif
                    {{-- ردیف نمایش پیوست‌ها (دقیقاً بعد از <tr> اصلی تسک) --}}
@if($opened_attachments_id === $task->id)
    <tr class="bg-orange-50">
        <td colspan="10" class="p-4 border-b">
            <div class="flex flex-col gap-3">
                <h5 class="font-bold text-sm text-orange-800 flex items-center gap-2">
                    📎 لیست پیوست‌های تسک:
                </h5>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    @forelse($task->attachments as $attach)
                        <div class="flex justify-between items-center bg-white p-2 rounded shadow-sm border border-orange-200">
                            <div class="flex flex-col">
                                <span class="text-xs font-medium text-gray-800">{{ Str::limit($attach->file_name, 30) }}</span>
                                <span class="text-[10px] text-gray-500">
                                    توسط: {{ $attach->user->full_name }} | {{ number_format($attach->file_size / 1024, 1) }} KB
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ asset('storage/' . $attach->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-bold">دانلود</a>
                                {{-- دکمه حذف فایل فقط برای آپلودکننده (در آینده) --}}
                                <button wire:click="deleteAttachment({{ $attach->id }})" class="text-red-500 text-xs">حذف</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 italic">هیچ فایلی برای این تسک پیوست نشده است.</p>
                    @endforelse
                </div>

                {{-- بخش آپلود جدید برای گیرنده یا فرستنده در همان لحظه مشاهده --}}
            <div class="mt-3 p-3 bg-white rounded border border-dashed border-orange-300">
    <label class="block text-[11px] font-bold text-gray-600 mb-1">افزودن پیوست جدید:</label>
    <div class="flex items-center gap-2">
        {{-- ۱. کاربر ابتدا از اینجا فایل را انتخاب می‌کند --}}
        <input type="file" wire:model="files" multiple class="text-xs">
        {{-- نمایش خطاهای اعتبار سنجی فایل به صورت آنی --}}
@error('files.*') 
    <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> 
@enderror

{{-- نمایش خطای کلی (مثلاً محدودیت حجم کل) --}}
@error('files') 
    <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> 
@enderror
        {{-- ۲. بعد از انتخاب، این دکمه فایل‌های انتخاب شده را ذخیره می‌کند --}}
        <button wire:click="uploadMoreFiles({{ $task->id }})" 
                wire:loading.attr="disabled"
                @if(empty($files)) disabled @endif {{-- اگر فایلی انتخاب نشده دکمه غیرفعال باشد --}}
                class="bg-orange-600 text-white px-3 py-1 rounded text-xs shadow hover:bg-orange-700 disabled:opacity-50">
            تایید و آپلود نهایی
        </button>
    </div>
    
    {{-- نمایش پیش‌نمایش فایل‌های انتخاب شده قبل از آپلود قطعی --}}
    @if($files)
        <div class="text-[10px] text-blue-600 mt-1">
            {{ count($files) }} فایل آماده آپلود است.
        </div>
    @endif
</div>
            </div>
            
        </td>
    </tr>
@endif
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- صفحه‌بندی --}}
    <div class="p-4 bg-gray-50">
        {{ $tasks->links() }}
    </div>
</div>
</div>
