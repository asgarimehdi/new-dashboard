<div class="p-4 md:p-8 bg-gray-50 min-h-screen " dir="rtl">
    <div class="max-w-5xl mx-auto space-y-6">
        
        {{-- هدر --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">مدیریت شهرها</h1>
                <p class="text-sm text-gray-500 mt-1">مدیریت لیست شهرهای تابعه استان‌ها</p>
            </div>
            
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.500ms="search" 
                    placeholder="جستجو شهر یا استان..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- کارت فرم --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                {{-- انتخاب استان --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">انتخاب استان</label>
                    <select wire:model="province_id" class="w-full border-gray-200 rounded-lg px-3 py-2.5 focus:ring-emerald-500 focus:border-emerald-500 @error('province_id') border-red-500 @enderror">
                        <option value="">انتخاب کنید...</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                    @error('province_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- نام شهر --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">نام شهر</label>
                    <input type="text" wire:model="name" placeholder="مثلاً: ری"
                        class="w-full border-gray-200 rounded-lg px-4 py-2.5 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- دکمه‌ها --}}
                <div class="flex gap-2">
                    <button wire:click="save" 
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg transition-all flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        {{ $cityId ? 'بروزرسانی' : 'ثبت شهر' }}
                    </button>

                    @if($cityId)
                        <button wire:click="resetForm" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-lg transition-all">
                            انصراف
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- نمایش لیست - دسکتاپ --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase">
                        <th class="p-4 border-b">#</th>
                        <th class="p-4 border-b">نام شهر</th>
                        <th class="p-4 border-b">استان مربوطه</th>
                        <th class="p-4 border-b text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($cities as $city)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="p-4 text-gray-400 text-sm">{{ $city->id }}</td>
                            <td class="p-4 font-semibold">{{ $city->name }}</td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $city->province->name }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    <button wire:click="edit({{ $city->id }})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $city->id }})" class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400 italic">هیچ شهری پیدا نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- نمایش لیست - موبایل (کارت تکی) --}}
        <div class="md:hidden grid grid-cols-1 gap-4">
            @foreach($cities as $city)
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">کد: {{ $city->id }}</div>
                        <div class="font-bold text-gray-800">{{ $city->name }}</div>
                        <div class="text-sm text-emerald-600 mt-1 font-medium">{{ $city->province->name }}</div>
                    </div>
                    <div class="flex gap-1">
                        <button wire:click="edit({{ $city->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button wire:click="deleteConfirm({{ $city->id }})" class="p-2 bg-red-50 text-red-500 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- صفحه‌بندی --}}
        <div class="mt-4 shadow-sm rounded-xl overflow-hidden">
            {{ $cities->links() }}
        </div>
    </div>

    {{-- SweetAlert2 Scripts --}}
    <script>
        window.addEventListener('swal', event => {
            Swal.fire({
                title: event.detail[0].title,
                text: event.detail[0].text,
                icon: event.detail[0].icon,
                confirmButtonText: 'تایید',
                customClass: {
                    confirmButton: 'bg-emerald-600 text-white px-6 py-2 rounded-lg'
                }
            });
        });

        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: 'حذف شهر؟',
                text: "با حذف شهر، اطلاعات آن از سیستم پاک می‌شود.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'بله، حذف کن',
                cancelButtonText: 'بی‌خیال'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', event.detail.id)
                }
            })
        });
    </script>
</div>