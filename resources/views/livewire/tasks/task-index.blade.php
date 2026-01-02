<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت تسک‌ها</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
   <div class="bg-white p-6 rounded-lg shadow-md mb-6 border-t-4 border-indigo-500">

    <h3 class="font-bold text-xl mb-6 text-gray-800 border-b pb-2">
        {{ $taskId ? 'ویرایش تسک: ' . $title : 'ثبت تسک جدید' }}
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- عنوان تسک --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">عنوان تسک</label>
            <input type="text" wire:model="title" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-300 outline-none">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- توضیحات --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">توضیحات (اختیاری)</label>
            <textarea wire:model="description" rows="3" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
        </div>

        {{-- انتخاب واحد --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">واحد مربوطه</label>
            <select wire:model="unit_id" class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">انتخاب کنید...</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
            @error('unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- اولویت --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">اولویت</label>
            <select wire:model="priority" class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="low">کم</option>
                <option value="normal">معمولی</option>
                <option value="urgent">فوری</option>
            </select>
            @error('priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

      
        <div>
{{--upload files--}}
<div class="md:col-span-2" 
     x-data="{ isUploading: false, progress: 0 }" 
     x-on:livewire-upload-start="isUploading = true" 
     x-on:livewire-upload-finish="isUploading = false" 
     x-on:livewire-upload-error="isUploading = false" 
     x-on:livewire-upload-progress="progress = $event.detail.progress">

    <label class="block text-sm font-bold text-gray-700 mb-2">ضمائم (اختیاری - حداکثر ۱۰ مگابایت مجموع)</label>
    
    <div class="flex items-center justify-center w-full">
        <label class="flex flex-col w-full h-32 border-4 border-dashed border-gray-200 hover:bg-gray-100 hover:border-indigo-300 transition duration-300 cursor-pointer">
            <div class="flex flex-col items-center justify-center pt-7">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="pt-1 text-sm text-gray-400">انتخاب فایل‌ها (چندگانه)</p>
            </div>
            
            <input type="file" wire:model="files" multiple class="opacity-0" />
        </label>
    </div>

    <div x-show="isUploading" class="mt-4" x-cloak>
        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-200" 
                 :style="`width: ${progress}%` text-align: center;">
            </div>
        </div>
        <div class="flex justify-between mt-1">
            <span class="text-[10px] text-indigo-600 font-bold" x-text="`در حال آپلود: ${progress}%` "></span>
            <span class="text-[10px] text-gray-400 italic">لطفاً صبر کنید...</span>
        </div>
    </div>

    {{-- نمایش خطاها --}}
    @error('files.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
    @error('files') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

    {{-- لیست فایل‌های انتخاب شده --}}
    @if($files)
        <div class="mt-2 grid grid-cols-2 gap-2">
            @foreach($files as $index => $file)
                <div class="flex items-center justify-between bg-indigo-50 p-2 rounded border border-indigo-100 shadow-sm">
                    <span class="text-[10px] text-indigo-700 truncate max-w-[150px]">{{ $file->getClientOriginalName() }}</span>
                    <button type="button" wire:click="removeFile({{ $index }})" class="text-red-500 hover:bg-red-50 p-1 rounded-full">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>
  {{-- مهلت انجام --}}
<div class="col-span-1" wire:ignore>
    <label class="block text-sm font-bold text-gray-700 mb-1">مهلت انجام (شمسی)</label>
    <input 
        data-jdp 
        type="text" 
        id="due_date_picker"
        class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-300" 
        placeholder="انتخاب تاریخ..."
        autocomplete="off"
    >
</div>

<script>
    // فعال‌سازی انتخابگر تاریخ
    jalaliDatepicker.startWatch();

    // گوش دادن به تغییرات اینپوت و ارسال به لایووایر
    document.getElementById('due_date_picker').addEventListener('jdp:change', function (e) {
        let date = e.target.value;
        // ارسال مقدار به متغیر due_date در لایووایر
        @this.set('due_date', date);
    });

    // برای زمانی که از حالت ویرایش خارج می‌شویم یا فرم ریست می‌شود
    window.addEventListener('reset-datepicker', event => {
        document.getElementById('due_date_picker').value = '';
    });
    
    // برای پر کردن اینپوت هنگام کلیک روی دکمه ویرایش
    window.addEventListener('set-datepicker', event => {
        document.getElementById('due_date_picker').value = event.detail.value;
    });
    // تنظیم مقدار وقتی دکمه ویرایش زده می‌شود
    window.addEventListener('set-datepicker', event => {
        const input = document.getElementById('due_date_picker');
        if (input) input.value = event.detail.value;
    });

    // خالی کردن مقدار وقتی انصراف یا ذخیره زده می‌شود
    window.addEventListener('reset-datepicker', event => {
        const input = document.getElementById('due_date_picker');
        if (input) input.value = '';
    });
</script>
            @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- بخش ارجاع مستقیم (فقط در حالت ایجاد تسک جدید نمایش داده می‌شود) --}}
        @if(!$taskId)
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
            <label class="block text-sm font-bold text-indigo-900 mb-2">ارجاع مستقیم به کاربر</label>
            <input type="text" wire:model.live.debounce.300ms="assign_user_search" 
                   class="w-full border rounded px-3 py-2 text-sm mb-2" placeholder="جستجوی نام...">
            
            @if($assign_user_search)
                <ul class="border rounded bg-white max-h-40 overflow-y-auto shadow-sm mb-2">
                    @forelse($assignableUsers as $user)
                        <li wire:click="$set('assign_user_id', {{ $user->id }})" 
                            class="px-3 py-2 hover:bg-indigo-100 cursor-pointer text-sm border-b last:border-0">
                            {{ $user->full_name }}
                        </li>
                    @empty
                        <li class="px-3 py-2 text-gray-500 text-sm">کاربری یافت نشد</li>
                    @endforelse
                </ul>
            @endif

            @if($assign_user_id)
                @php $selectedUser = \App\Models\User::find($assign_user_id); @endphp
                @if($selectedUser)
                    <div class="flex justify-between items-center bg-white p-2 rounded border border-green-200">
                        <span class="text-xs text-green-700 font-bold italic">آماده ارجاع به: {{ $selectedUser->full_name }}</span>
                        <button wire:click="$set('assign_user_id', null)" class="text-red-500 text-xs">حذف</button>
                    </div>
                @endif
            @endif
        </div>
        @endif
    </div>
@if($taskId)
    <div class="mt-4 border-t pt-2">
        <label class="block text-xs font-bold text-gray-600 mb-2">فایل‌های پیوست شده فعلی:</label>
        <div class="space-y-2">
            @foreach(\App\Models\Attachment::where('task_id', $this->taskId)->get() as $attach)
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded border">
                    <span class="text-xs text-gray-700">{{ $attach->file_name }}</span>
                    <button type="button" wire:click="deleteAttachment({{ $attach->id }})" class="text-red-500 hover:text-red-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif
    {{-- دکمه‌های عملیاتی --}}
    <div class="mt-6 flex gap-3 border-t pt-4">
        <button wire:click="save" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow transition duration-200">
            {{ $taskId ? 'بروزرسانی تغییرات' : 'ثبت و سازماندهی' }}
        </button>
        
        @if($taskId)
           <button wire:click="cancelEdit" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
    انصراف از ویرایش
</button>
        @endif
    </div>
</div>
@if($assign_task_id)
<div class="bg-indigo-50 p-3 rounded mb-3 space-y-2">

    <strong>ارجاع تسک</strong>

    {{-- input جستجو --}}
    <input
        type="text"
        wire:model.live.debounce.300ms="assign_user_search"
        class="border rounded px-2 py-1 w-full"
        placeholder="جستجوی نام کاربر..."
    >

    {{-- لیست نتایج --}}
    @if($assign_user_search)
        <ul class="border rounded bg-white max-h-48 overflow-y-auto">
            @forelse($assignableUsers as $user)
                <li
                    wire:click="$set('assign_user_id', {{ $user->id }})"
                    class="px-3 py-2 hover:bg-indigo-100 cursor-pointer"
                >
                    {{ $user->full_name }}
                </li>
            @empty
                <li class="px-3 py-2 text-gray-500">
                    کاربری یافت نشد
                </li>
            @endforelse
        </ul>
    @endif

    {{-- کاربر انتخاب‌شده --}}
    @if($assign_user_id)
        @php
            $selectedUser = \App\Models\User::find($assign_user_id);
        @endphp

        <div class="text-sm text-green-700">
            انتخاب‌شده:
            <strong>{{ $selectedUser->full_name }}</strong>
        </div>
    @endif

    <div class="flex gap-2 pt-2">
        <button
            wire:click="assignTask"
            class="bg-indigo-600 text-white px-3 py-1 rounded"
        >
            ثبت ارجاع
        </button>

        <button
            wire:click="
                $set('assign_task_id', null);
                $set('assign_user_id', null);
                $set('assign_user_search', '');
            "
            class="text-gray-600"
        >
            انصراف
        </button>
    </div>

</div>
@endif
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
                                    $priorityClasses = [
                                        'urgent' => 'bg-red-600 text-white',
                                        'normal' => 'bg-blue-100 text-blue-800',
                                        'low'    => 'bg-gray-200 text-gray-700'
                                    ];
                                    $priorityLabels = ['urgent' => 'فوری', 'normal' => 'معمولی', 'low' => 'کم'];
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $priorityClasses[$task->priority] ?? $priorityClasses['normal'] }}">
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
