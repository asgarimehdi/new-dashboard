<div class="p-4 md:p-8 bg-gray-50 min-h-screen" x-data="{ isOpen: @entangle('isModalOpen') }">
    <div class="max-w-6xl mx-auto space-y-6">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">مدیریت انواع واحدها</h1>
                <p class="text-sm text-gray-500 mt-1">تعریف و ویرایش دسته‌بندی‌های ساختار سازمانی</p>
            </div>
            <button @click="$wire.openModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition shadow-md group">
                <svg class="w-5 h-5 ml-2 group-hover:rotate-90 transition shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                افزودن نوع جدید
            </button>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="جستجو در عناوین..." class="w-full pr-10 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
        </div>

        {{-- Table / Cards --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Desktop View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">شناسه</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">عنوان نوع واحد</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">توضیحات</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($types as $type)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $type->id }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-800">{{ $type->title }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($type->description, 50) ?: '---' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="edit({{ $type->id }})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition" title="ویرایش">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:click="deleteConfirm({{ $type->id }})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition" title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-400">اطلاعاتی یافت نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile View --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($types as $type)
                    <div class="p-4 space-y-3 font-sans">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-bold text-gray-800">{{ $type->title }}</span>
                            <span class="text-xs text-gray-400 font-mono">#{{ $type->id }}</span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $type->description ?: 'بدون توضیحات' }}</p>
                        <div class="flex justify-end gap-4 pt-2 border-t border-gray-50 text-sm font-medium">
                            <button wire:click="edit({{ $type->id }})" class="text-blue-600">ویرایش</button>
                            <button wire:click="deleteConfirm({{ $type->id }})" class="text-red-600">حذف</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            {{ $types->links() }}
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="isOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="isOpen = false">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">{{ $unitTypeId ? 'ویرایش نوع واحد' : 'ثبت نوع جدید' }}</h3>
                <button @click="isOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">عنوان</label>
                    <input type="text" wire:model="title" class="w-full border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm" placeholder="مثلاً: بیمارستان، دانشکده...">
                    @error('title') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">توضیحات</label>
                    <textarea wire:model="description" rows="3" class="w-full border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm" placeholder="توضیحات اختیاری..."></textarea>
                    @error('description') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="p-6 bg-gray-50 flex gap-3">
                <button wire:click="save" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition shadow-md">
                    {{ $unitTypeId ? 'اعمال تغییرات' : 'ذخیره اطلاعات' }}
                </button>
                <button @click="isOpen = false" class="flex-1 bg-white border border-gray-200 text-gray-600 font-bold py-2 rounded-lg hover:bg-gray-100 transition">انصراف</button>
            </div>
        </div>
    </div>
</div>