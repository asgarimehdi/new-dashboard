<div class="p-4 md:p-8 bg-gray-50 min-h-screen ">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">مدیریت استان‌ها</h1>
                <p class="text-sm text-gray-500 mt-1">تعریف و ویرایش محدوده جغرافیایی استان‌ها</p>
            </div>
            
            {{-- فیلد جستجو در بالا --}}
            <div class="relative w-full md:w-72">
                <input type="text" wire:model.live.debounce.500ms="search" 
                    placeholder="جستجو در نام استان..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border-none shadow-sm focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- کارت فرم ثبت و ویرایش --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">نام استان</label>
                    <input type="text" wire:model="name" 
                        class="w-full border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="مثلاً: تهران">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex gap-2 w-full md:w-auto">
                    <button wire:click="save" 
                        class="flex-1 md:w-32 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition-all flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $provinceId ? 'بروزرسانی' : 'ثبت استان' }}
                    </button>

                    @if($provinceId)
                        <button wire:click="resetForm" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-lg transition-all">
                            انصراف
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- جدول نمایش داده‌ها --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase">
                        <th class="p-4 font-bold border-b">شناسه</th>
                        <th class="p-4 font-bold border-b">نام استان</th>
                        <th class="p-4 font-bold border-b text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse ($provinces as $province)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="p-4 text-gray-400">#{{ $province->id }}</td>
                            <td class="p-4 font-medium">{{ $province->name }}</td>
                            <td class="p-4">
                                <div class="flex justify-center gap-3">
                                    <button wire:click="edit({{ $province->id }})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="ویرایش">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.25 2.25 0 113.182 3.182L12 18.75l-3 1 1-3 9.586-9.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $province->id }})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400">موردی برای نمایش یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                {{ $provinces->links() }}
            </div>
        </div>
    </div>

    {{-- اسکریپت تایید حذف با SweetAlert2 --}}
    <script>
        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این عمل غیرقابل بازگشت است!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', event.detail.id)
                }
            })
        });
    </script>
</div>