<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        
        {{-- پیام موفقیت --}}
       {{-- این بخش جایگزین پیام موفقیت قبلی در Blade شود --}}
@if (session()->has('success'))
    <div class="mb-8 p-6 bg-white border-2 border-green-500 rounded-3xl shadow-lg shadow-green-100 flex flex-col md:flex-row items-center justify-between gap-4 animate-fade-in">
        <div class="flex items-center gap-4">
            <div class="bg-green-500 text-white p-3 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">درخواست با موفقیت ثبت شد</h3>
                <p class="text-gray-500 text-sm italic">کارشناسان ما به زودی درخواست شما را بررسی خواهند کرد.</p>
            </div>
        </div>
        <div class="bg-green-50 px-6 py-3 rounded-2xl border border-green-100 flex flex-col items-center">
            <span class="text-[10px] text-green-600 font-bold mb-1">کد پیگیری شما:</span>
            <span class="text-xl font-black text-green-700 tracking-widest">{{ session('ticket_code') ?? 'TIC-XXXX' }}</span>
        </div>
    </div>
@endif

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-indigo-600 p-6 text-white">
                <h2 class="text-2xl font-black flex items-center gap-3">
                    <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    ثبت تیکت پشتیبانی جدید
                </h2>
                <p class="text-indigo-100 mt-2 text-sm italic">لطفاً جزئیات درخواست خود را با دقت وارد نمایید.</p>
            </div>

            <form wire:submit.prevent="saveTicket" class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- واحد مقصد --}}
                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2 italic">واحد مقصد <span class="text-red-500">*</span></label>
                        <select wire:model="unit_id" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-100 outline-none transition-all">
                            <option value="">انتخاب واحد مربوطه...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- اولویت --}}
                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2 italic">میزان فوریت</label>
                        <div class="flex p-1 bg-gray-100 rounded-xl">
                            @foreach(['low' => 'عادی', 'normal' => 'متوسط', 'urgent' => 'فوری'] as $key => $label)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="priority" value="{{ $key }}" class="hidden peer">
                                    <div class="py-2 text-center text-xs font-bold rounded-lg transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-gray-500">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- موضوع --}}
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2 italic">موضوع تیکت <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="subject" placeholder="خلاصه‌ای از مشکل را وارد کنید..." 
                           class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-100 outline-none transition-all">
                    @error('subject') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- متن پیام --}}
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2 italic">شرح کامل درخواست <span class="text-red-500">*</span></label>
                    <textarea wire:model="content" rows="6" placeholder="جزئیات دقیق را برای ما بنویسید..." 
                              class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-100 outline-none transition-all"></textarea>
                    @error('content') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- بخش فایل با مدیریت وضعیت آپلود --}}
                <div x-data="{ isUploading: false, progress: 0 }" 
                     x-on:livewire-upload-start="isUploading = true" 
                     x-on:livewire-upload-finish="isUploading = false" 
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
                    
                    <label class="block text-sm font-black text-gray-700 mb-2 italic">ضمائم و تصاویر (حداکثر ۱۰MB)</label>
                    <div class="relative group border-2 border-dashed border-gray-200 rounded-2xl p-8 transition-all hover:border-indigo-400 hover:bg-indigo-50/50">
                        <input type="file" wire:model="files" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <p class="mt-2 text-sm text-gray-600 font-bold">فایل‌ها را به اینجا بکشید یا کلیک کنید</p>
                        </div>
                    </div>

                    {{-- نوار پیشرفت --}}
                    <div x-show="isUploading" class="mt-4">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 transition-all duration-300" :style="`width: ${progress}%` text-align: center;"></div>
                        </div>
                    </div>

                    {{-- لیست فایل‌های انتخاب شده --}}
                    @if($files)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($files as $file)
                                <div class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-bold border border-indigo-100 flex items-center gap-1 shadow-sm">
                                    <svg class="w-3 h-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"></path></svg>
                                    {{ $file->getClientOriginalName() }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- دکمه ارسال --}}
                <div class="pt-6">
                    <button type="submit" wire:loading.attr="disabled" 
                            class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-lg shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center justify-center gap-2 group">
                        <span wire:loading.remove>ارسال درخواست و دریافت کد رهگیری</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            در حال ثبت...
                        </span>
                        <svg wire:loading.remove class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>