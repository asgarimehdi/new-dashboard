<div class="p-6 max-w-7xl mx-auto space-y-6 text-right" dir="rtl">
    <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">تنظیمات سطوح دسترسی و نقش‌ها</h1>

    @if(session()->has('success'))
        <div class="bg-green-600 text-white p-3 rounded-lg shadow-md mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- فرم تعریف نقش جدید --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg border border-gray-200 h-fit">
            <h2 class="text-lg font-bold mb-4 text-blue-700">{{ $roleId ? 'ویرایش نقش' : 'تعریف نقش جدید' }}</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-1">عنوان نقش (به انگلیسی یا فارسی)</label>
                    <input type="text" wire:model="name" placeholder="مثلاً: مدیر انبار" 
                           class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">انتخاب دسترسی‌های این نقش:</label>
                    <div class="bg-gray-50 p-4 rounded-lg border max-h-64 overflow-y-auto space-y-2">
                        @foreach($permissions as $perm)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded transition">
                                <input type="checkbox" wire:model="selected_permissions" value="{{ $perm->name }}" class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm text-gray-700">{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selected_permissions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2">
                    <button wire:click="save" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold">
                        {{ $roleId ? 'بروزرسانی' : 'ثبت نقش' }}
                    </button>
                    @if($roleId)
                        <button wire:click="resetForm" class="px-4 py-2 bg-gray-200 rounded-lg">انصراف</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- لیست نقش‌های موجود --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4">عنوان نقش</th>
                        <th class="p-4">دسترسی‌ها</th>
                        <th class="p-4 text-left">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($roles as $role)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-gray-700">{{ $role->name }}</td>
                            <td class="p-4 text-sm text-gray-500">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions as $p)
                                        <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[11px] border border-blue-100 italic">
                                            {{ $p->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4 text-left flex justify-end gap-3">
                                <button wire:click="edit({{ $role->id }})" class="text-blue-500 hover:text-blue-700">ویرایش</button>
                                <button onclick="confirm('با حذف نقش، دسترسی کاربران مرتبط حذف می‌شود. مطمئن هستید؟') || event.stopImmediatePropagation()" 
                                        wire:click="delete({{ $role->id }})" class="text-red-400">حذف</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>