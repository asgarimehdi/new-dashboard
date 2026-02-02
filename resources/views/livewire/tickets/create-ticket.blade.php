<div class="py-6 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen"
     x-data="{ 
        isUploading: false, 
        progress: 0, 
        errorMessage: '',
        validateFiles(files) {
            this.errorMessage = ''; 
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/zip', 'application/x-rar-compressed'];
            const maxFileSize = 5 * 1024 * 1024;
            let totalSize = 0;
            if (files.length === 0) return true;
            if (files.length > 5) { this.errorMessage = 'حداکثر ۵ فایل مجاز است.'; return false; }
            for (let i = 0; i < files.length; i++) {
                if (!allowedTypes.includes(files[i].type) && files[i].type !== '') {
                    this.errorMessage = 'فرمت ' + files[i].name + ' مجاز نیست.'; return false;
                }
                if (files[i].size > maxFileSize) {
                    this.errorMessage = 'حجم ' + files[i].name + ' بیش از ۵ مگ است.'; return false;
                }
                totalSize += files[i].size;
            }
            if (totalSize > 10 * 1024 * 1024) { this.errorMessage = 'مجموع حجم نباید بیش از ۱۰ مگابایت باشد.'; return false; }
            return true;
        },
        resetUpload() {
            this.errorMessage = ''; this.isUploading = false; this.progress = 0; $wire.set('files', []);
        }
     }">
    
    <div class="max-w-4xl mx-auto">
        {{-- پیام موفقیت فشرده --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-500 text-white rounded-2xl shadow-lg flex items-center justify-between animate-fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-bold">ثبت شد. کد پیگیری: {{ session('ticket_code') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
            {{-- Header فشرده --}}
            <div class="bg-indigo-700 p-5 text-white flex justify-between items-center relative overflow-hidden">
                <div class="relative z-10 text-right">
                    <h2 class="text-xl font-black flex items-center gap-2">ثبت درخواست جدید</h2>
                </div>
                <div class="absolute -left-10 -top-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            </div>

            <form wire:submit.prevent="saveTicket" class="p-6 md:p-8 space-y-5">
                {{-- ردیف اول: واحد و اولویت --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-right">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-indigo-900 mr-1">واحد مقصد:</label>
                        <select wire:model="unit_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:border-indigo-500 outline-none transition-all text-sm">
                            <option value="">انتخاب کنید...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-indigo-900 mr-1">میزان فوریت:</label>
                        <div class="flex bg-gray-100 p-1 rounded-xl">
                            @foreach(['low' => 'عادی', 'normal' => 'متوسط', 'urgent' => 'فوری'] as $key => $label)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="priority" value="{{ $key }}" class="hidden peer">
                                    <div class="py-1.5 text-center text-[11px] font-black rounded-lg transition-all peer-checked:bg-white peer-checked:text-indigo-700 peer-checked:shadow-sm text-gray-500">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ردیف دوم: موضوع --}}
                <div class="space-y-1 text-right text-indigo-900">
                    <label class="block text-xs font-bold mr-1">موضوع تیکت:</label>
                    <input type="text" wire:model="subject" placeholder="عنوان کوتاه مشکل..." 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:border-indigo-500 outline-none text-sm">
                    @error('subject') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- ردیف سوم: متن پیام --}}
                <div class="space-y-1 text-right text-indigo-900">
                    <label class="block text-xs font-bold mr-1">شرح درخواست:</label>
                    <textarea wire:model="content" rows="3" placeholder="جزئیات درخواست..." 
                              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:border-indigo-500 outline-none text-sm resize-none"></textarea>
                    @error('content') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- بخش آپلود فشرده --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <div class="relative group border-2 border-dashed rounded-2xl p-4 transition-all text-center"
                         :class="errorMessage ? 'border-red-300 bg-red-50' : 'border-indigo-100 bg-indigo-50/30 hover:bg-indigo-50'">
                        
                        <input type="file" wire:model="files" multiple class="absolute inset-0 opacity-0 cursor-pointer"
                               x-on:livewire-upload-start="isUploading = true; errorMessage = ''"
                               x-on:livewire-upload-finish="isUploading = false"
                               x-on:livewire-upload-progress="progress = $event.detail.progress"
                               @change="if(!validateFiles($event.target.files)) { $event.target.value = ''; resetUpload(); }">

                        <div x-show="!isUploading" class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <span class="text-xs text-indigo-600 font-bold">پیوست فایل (حداکثر ۵ مورد)</span>
                        </div>

                        <div x-show="isUploading" class="w-full space-y-2">
                            <div class="w-full bg-gray-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-full transition-all" :style="'width:' + progress + '%'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- نمایش فایل‌های انتخاب شده (خیلی کوچک) --}}
                    <div class="flex flex-wrap gap-2">
                        @if($files && !$errors->has('files'))
                            @foreach($files as $index => $file)
                                <div class="relative bg-white p-1 rounded-lg border border-indigo-100 shadow-sm animate-fade-in flex items-center gap-1">
                                    <span class="text-[9px] max-w-[60px] truncate text-gray-600">{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeFile({{ $index }})" class="text-red-500 font-bold px-1 text-xs">&times;</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- خطا و دکمه نهایی --}}
                <div class="pt-4 border-t border-gray-50 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div x-show="errorMessage" class="text-red-600 text-[10px] font-bold">
                         * <span x-text="errorMessage"></span>
                    </div>
                    <button type="submit" 
                            :disabled="isUploading || errorMessage !== ''"
                            class="w-full md:w-auto bg-indigo-700 hover:bg-indigo-800 text-white px-8 py-3 rounded-xl font-black transition-all flex items-center justify-center gap-2 disabled:bg-gray-400 text-sm">
                        <span>ثبت نهایی درخواست</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>