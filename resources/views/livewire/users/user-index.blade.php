<div class="p-6 max-w-7xl mx-auto space-y-6 text-right" dir="rtl">
    <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">مدیریت پیشرفته کاربران</h1>

    {{-- نمایش پیام موفقیت --}}
    @if(session()->has('success'))
        <div class="bg-green-600 text-white p-3 rounded-lg shadow-md mb-4"> {{ session('success') }} </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- ستون راست: اطلاعات --}}
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-1">نام و نام خانوادگی</label>
                <input type="text" wire:model="full_name" class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                @error('full_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">کد ملی</label>
                <input type="text" wire:model="national_code" class="w-full border rounded-lg p-2.5 text-left font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                @error('national_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- فیلد جستجوی واحد هوشمند --}}
            <div class="relative">
                <label class="block text-sm font-bold mb-1">واحد سازمانی</label>
                <div class="flex items-center border rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                    <input type="text" 
                           wire:model.live="unit_search" 
                           placeholder="{{ $selected_unit_name ?: 'جستجوی واحد...' }}" 
                           class="w-full p-2.5 outline-none bg-white">
                    @if($selected_unit_name)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 m-1 rounded text-xs whitespace-nowrap">{{ $selected_unit_name }}</span>
                    @endif
                </div>
                
                @if($show_dropdown && count($units) > 0)
                    <div class="absolute z-50 w-full bg-white border rounded-b-lg shadow-xl mt-1 overflow-hidden">
                        @foreach($units as $unit)
                            <div wire:click="selectUnit({{ $unit->id }}, '{{ $unit->name }}')" 
                                 class="p-3 hover:bg-blue-50 cursor-pointer border-b last:border-0 transition">
                                {{ $unit->name }}
                            </div>
                        @endforeach
                    </div>
                @endif
                <input type="hidden" wire:model="unit_id">
                @error('unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- ستون چپ: نقش‌ها و سوئیچ --}}
        <div class="space-y-5 border-r pr-6">
            <div>
                <label class="block text-sm font-bold mb-3 text-blue-700">تعیین نقش‌های دسترسی</label>
                <div class="grid grid-cols-2 gap-3 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" wire:model="selected_roles" value="{{ $role->name }}" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm group-hover:text-blue-600 transition">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('selected_roles') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                <span class="text-sm font-bold text-blue-900">وضعیت فعالیت کاربر:</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:-translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button wire:click="save" class="flex-1 bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 shadow-lg font-bold transition">
                    {{ $userId ? 'ذخیره تغییرات' : 'ایجاد حساب کاربر' }}
                </button>
                @if($userId)
                    <button wire:click="resetForm" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">انصراف</button>
                @endif
            </div>
        </div>
    </div>

    {{-- جدول کاربران --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mt-8">
        <div class="p-4 bg-gray-50 border-b flex items-center justify-between">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="جستجوی نام یا کد ملی در کل لیست..." class="w-full max-w-md border rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <table class="w-full text-right">
            <thead>
                <tr class="bg-gray-100 text-sm text-gray-600 uppercase">
                    <th class="p-4">نام کاربر</th>
                    <th class="p-4 text-center">واحد</th>
                    <th class="p-4 text-center">نقش‌ها</th>
                    <th class="p-4 text-center">وضعیت</th>
                    <th class="p-4 text-left">مدیریت</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr class="hover:bg-blue-50/50 transition">
                        <td class="p-4 font-bold text-gray-700">{{ $user->full_name }} <div class="text-[10px] font-normal text-gray-400 font-mono">{{ $user->national_code }}</div></td>
                        <td class="p-4 text-center"><span class="bg-white border px-3 py-1 rounded-full text-xs text-gray-600 shadow-sm">{{ $user->unit?->name }}</span></td>
                        <td class="p-4 text-center italic">
                            @foreach($user->roles as $role)
                                <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100 ml-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="p-4 text-center">
                            <span class="w-2.5 h-2.5 inline-block rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} ml-1"></span>
                            <span class="text-xs {{ $user->is_active ? 'text-green-700' : 'text-red-700' }} font-bold">{{ $user->is_active ? 'فعال' : 'مسدود' }}</span>
                        </td>
                        <td class="p-4 text-left font-medium">
                            <button wire:click="edit({{ $user->id }})" class="text-blue-600 hover:underline ml-4">ویرایش</button>
                            <button onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="text-red-400 hover:text-red-600">حذف</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $users->links() }}</div>
    </div>
</div>