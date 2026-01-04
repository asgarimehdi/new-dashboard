<div class="py-10 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen font-[vazir]"
     x-data="{ 
        isUploading: false, 
        progress: 0, 
        errorMessage: '',
       validateFiles(files) {
    // ۱. بلافاصله در شروع هر انتخاب، خطا را خالی کن
    this.errorMessage = ''; 
    
    const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/zip', 'application/x-rar-compressed'];
    const maxFileSize = 5 * 1024 * 1024;
    let totalSize = 0;

    if (files.length === 0) return true;

    if (files.length > 5) {
        this.errorMessage = 'حداکثر ۵ فایل مجاز است.';
        return false;
    }

    for (let i = 0; i < files.length; i++) {
        // چک کردن نوع فایل با دقت بیشتر
        if (!allowedTypes.includes(files[i].type) && files[i].type !== '') {
            this.errorMessage = 'فرمت فایل ' + files[i].name + ' مجاز نیست.';
            return false;
        }
        if (files[i].size > maxFileSize) {
            this.errorMessage = 'حجم فایل ' + files[i].name + ' بیش از ۵ مگابایت است.';
            return false;
        }
        totalSize += files[i].size;
    }

    if (totalSize > 10 * 1024 * 1024) {
        this.errorMessage = 'مجموع حجم فایل‌ها نباید بیش از ۱۰ مگابایت باشد.';
        return false;
    }

    // اگر همه چیز درست بود، مطمئن شو کهerrorMessage خالی است
    this.errorMessage = '';
    return true;
},
        resetUpload() {
            this.errorMessage = '';
            this.isUploading = false;
            this.progress = 0;
            $wire.set('files', []);
        }
     }">
    
    <div class="max-w-4xl mx-auto">
        {{-- پیام موفقیت --}}
        @if (session()->has('success'))
            <div class="mb-8 p-6 bg-white border-r-8 border-green-500 rounded-3xl shadow-lg shadow-green-100/50 flex flex-col md:flex-row items-center justify-between gap-4 animate-fade-in">
                <div class="flex items-center gap-4">
                    <div class="bg-green-500 text-white p-3 rounded-2xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800">درخواست با موفقیت ثبت شد</h3>
                        <p class="text-gray-500 text-sm italic">کد پیگیری شما جهت مراجعات بعدی:</p>
                    </div>
                </div>
                <div class="bg-green-50 px-8 py-3 rounded-2xl border border-green-100 text-center">
                    <span class="text-2xl font-black text-green-700 tracking-widest">{{ session('ticket_code') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 overflow-hidden border border-gray-100">
            {{-- Header --}}
            <div class="bg-indigo-700 p-8 text-white relative overflow-hidden">
                <div class="relative z-10 text-right">
                    <h2 class="text-3xl font-black mb-1 flex items-center gap-3">
                        ثبت درخواست جدید
                    </h2>
                    <p class="text-indigo-100 opacity-80 font-light italic">لطفاً تمامی موارد را با دقت تکمیل کنید</p>
                </div>
                <div class="absolute -left-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <form wire:submit.prevent="saveTicket" class="p-8 md:p-12 space-y-8">
                {{-- سطرهای اصلی --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-right">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 mr-2 italic text-indigo-900 uppercase">واحد مقصد:</label>
                        <select wire:model="unit_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-4 focus:bg-white focus:border-indigo-500 outline-none transition-all shadow-sm">
                            <option value="">انتخاب واحد مربوطه...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-red-500 text-xs font-bold mr-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 mr-2 italic text-indigo-900">میزان فوریت:</label>
                        <div class="flex bg-gray-100 p-1.5 rounded-2xl">
                            @foreach(['low' => 'عادی', 'normal' => 'متوسط', 'urgent' => 'فوری'] as $key => $label)
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" wire:model="priority" value="{{ $key }}" class="hidden peer">
                                    <div class="py-3 text-center text-xs font-black rounded-xl transition-all peer-checked:bg-white peer-checked:text-indigo-700 peer-checked:shadow-md text-gray-400">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-2 text-right text-indigo-900">
                    <label class="block text-sm font-bold mr-2 italic uppercase">موضوع تیکت:</label>
                    <input type="text" wire:model="subject" placeholder="مثال: عدم دسترسی به سامانه آماری" 
                           class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-4 focus:bg-white focus:border-indigo-500 outline-none transition-all shadow-sm">
                    @error('subject') <span class="text-red-500 text-xs font-bold mr-2">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2 text-right text-indigo-900">
                    <label class="block text-sm font-bold mr-2 italic uppercase">شرح درخواست:</label>
                    <textarea wire:model="content" rows="5" placeholder="جزئیات دقیق مشکل خود را بنویسید..." 
                              class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-4 focus:bg-white focus:border-indigo-500 outline-none transition-all shadow-sm resize-none"></textarea>
                    @error('content') <span class="text-red-500 text-xs font-bold mr-2">{{ $message }}</span> @enderror
                </div>

                {{-- بخش آپلود فایل --}}
                <div class="space-y-4">

                    <div class="flex items-center justify-between mr-2">
                        <label class="block text-sm font-bold text-indigo-900 italic">ضمائم و مستندات:</label>
                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold">حداکثر ۵ فایل - ۱۰MB</span>
                    </div>

                    <div class="relative group border-2 border-dashed rounded-3xl p-10 transition-all text-center"
                         :class="errorMessage ? 'border-red-300 bg-red-50' : 'border-indigo-200 bg-indigo-50/30 hover:border-indigo-400 hover:bg-indigo-50'">
                        
                        <input type="file" wire:model="files" multiple class="absolute inset-0 opacity-0 cursor-pointer"
                               x-on:livewire-upload-start="isUploading = true; errorMessage = ''"
                               x-on:livewire-upload-finish="isUploading = false"
                               x-on:livewire-upload-error="isUploading = false; errorMessage = 'خطا در آپلود! حجم فایل بیش از حد مجاز سرور است.'"
                               x-on:livewire-upload-progress="progress = $event.detail.progress"
                               @change="if(!validateFiles($event.target.files)) { $event.target.value = ''; resetUpload(); }">

                        <div x-show="!isUploading" class="space-y-3">
                            <svg class="mx-auto h-12 w-12 text-indigo-300 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="text-sm text-indigo-600 font-bold">برای انتخاب فایل کلیک کنید یا فایل را اینجا رها کنید</p>
                        </div>

                        <div x-show="isUploading" class="w-full space-y-3">
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-indigo-600 h-full transition-all duration-300 shadow-[0_0_10px_rgba(79,70,229,0.5)]" :style="'width:' + progress + '%'"></div>
                            </div>
                            <p class="text-xs font-black text-indigo-700 animate-pulse" x-text="'در حال بارگذاری ضمیمه: ' + progress + '%'"></p>
                        </div>
                    </div>

                    {{-- خطای آنی --}}
                    <div x-show="errorMessage" x-transition class="bg-red-50 text-red-700 px-4 py-3 rounded-2xl text-xs font-bold border border-red-100 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="errorMessage"></span>
                    </div>

                    {{-- نمایش بندانگشتی‌ها (فقط اگر خطایی نباشد) --}}
                    @if($files && !$errors->has('files'))
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4 mt-6">
                            @foreach($files as $index => $file)
                                <div class="relative group bg-white p-2 rounded-2xl border border-indigo-50 shadow-sm transition-all hover:shadow-md animate-fade-in">
                                    <button type="button" wire:click="removeFile({{ $index }})" 
                                            class="absolute -top-2 -left-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 z-10 transition-transform hover:scale-110">
                                        &times;
                                    </button>
                                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center border border-gray-50">
                                        @if(in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ $file->temporaryUrl() }}" class="object-cover w-full h-full">
                                        @else
                                            <div class="text-center">
                                                <svg class="w-8 h-8 mx-auto text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[9px] font-black text-indigo-400 uppercase mt-1 block">{{ $file->getClientOriginalExtension() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- دکمه نهایی --}}
                <div class="pt-8 border-t border-gray-50">
                    <button type="submit" 
                            :disabled="isUploading || errorMessage !== ''"
                            wire:loading.attr="disabled"
                            wire:target="saveTicket"
                            class="w-full bg-indigo-700 hover:bg-indigo-800 text-white px-10 py-5 rounded-[1.5rem] font-black shadow-2xl shadow-indigo-200 transition-all flex items-center justify-center gap-3 disabled:bg-gray-400 disabled:shadow-none disabled:scale-100 group">
                        
                        <span x-show="isUploading" class="flex items-center gap-2">
                             <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                             در حال پردازش ضمائم...
                        </span>

                        <span x-show="!isUploading" wire:loading.remove wire:target="saveTicket" class="flex items-center gap-2 italic">
                            ثبت نهایی و ارسال تیکت
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                        </span>

                        <span wire:loading wire:target="saveTicket" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">...</svg>
                            در حال ثبت اطلاعات...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>