<div class="p-4 md:p-6 max-w-7xl mx-auto space-y-6 text-right" dir="rtl">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-black text-gray-800">مدیریت ساختار سازمانی</h1>
            <p class="text-sm text-gray-500 mt-1">تعریف و ویرایش واحدهای زیرمجموعه و چیدمان سلسله‌مراتبی</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="relative w-full md:w-64">
                <input type="text" wire:model.live.debounce.400ms="search" 
                       placeholder="جستجوی سریع..." 
                       class="w-full pr-10 pl-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                <svg class="w-5 h-5 absolute right-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-4">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-6">
                <h2 class="text-lg font-bold text-gray-700 mb-6 flex items-center gap-2">
                    <span class="w-2 h-7 bg-blue-600 rounded-full"></span>
                    {{ $unitId ? 'ویرایش واحد' : 'ثبت واحد جدید' }}
                </h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">نام واحد</label>
                        <input type="text" wire:model="name" class="w-full border-gray-200 rounded-xl focus:border-blue-500 focus:ring-blue-500 py-2.5">
                        @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">نوع ساختار</label>
                        <select wire:model.live="unit_type_id" class="w-full border-gray-200 rounded-xl focus:border-blue-500 focus:ring-blue-500 py-2.5">
                            <option value="">انتخاب کنید...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                        @error('unit_type_id') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    @if(!$this->isNationalUnit())
                        <div class="grid grid-cols-2 gap-3 p-3 bg-blue-50 rounded-xl">
                            <div>
                                <label class="block text-xs font-bold text-blue-700 mb-1 font-medium">استان</label>
                                <select wire:model.live="province_id" class="w-full border-none rounded-lg text-sm shadow-sm">
                                    <option value="">انتخاب...</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-blue-700 mb-1 font-medium">شهر</label>
                                <select wire:model="city_id" class="w-full border-none rounded-lg text-sm shadow-sm">
                                    <option value="">انتخاب...</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    @if($this->requiresParent())
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">واحد بالادست (والد)</label>
                            <select wire:model="parent_id" class="w-full border-gray-200 rounded-xl focus:border-blue-500 focus:ring-blue-500 py-2.5 {{ $parents->isEmpty() ? 'bg-gray-100' : '' }}">
                                <option value="">انتخاب واحد والد...</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->type->title }})</option>
                                @endforeach
                            </select>
                            @if($parents->isEmpty() && $unit_type_id)
                                <p class="text-amber-600 text-[11px] mt-1 font-medium">⚠️ هیچ والد مجازی برای این نوع واحد تعریف نشده است.</p>
                            @endif
                            @error('parent_id') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center gap-3 py-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:-translate-x-full rtl:peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                        <span class="text-sm font-medium text-gray-700">واحد فعال باشد</span>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button wire:click="{{ $unitId ? 'update' : 'save' }}" 
                                wire:loading.attr="disabled"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all disabled:opacity-50">
                            <span wire:loading.remove>{{ $unitId ? 'بروزرسانی تغییرات' : 'ثبت در سیستم' }}</span>
                            <span wire:loading>در حال پردازش...</span>
                        </button>
                        
                        @if($unitId)
                            <button wire:click="resetForm" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                                انصراف
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="p-4 text-sm font-bold text-gray-600">مشخصات واحد</th>
                            <th class="p-4 text-sm font-bold text-gray-600">موقعیت/والد</th>
                            <th class="p-4 text-sm font-bold text-gray-600 text-center">وضعیت</th>
                            <th class="p-4 text-sm font-bold text-gray-600 text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($units as $unit)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $unit->name }}</div>
                                    <div class="text-[11px] text-blue-600 font-medium mt-0.5 bg-blue-50 inline-block px-2 rounded-md">{{ $unit->type->title }}</div>
                                </td>
                                <td class="p-4 font-medium">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            📍 {{ $unit->city?->name ?? 'سطح ملی' }}
                                        </span>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            🌳 والد: {{ $unit->parent?->name ?? 'ریشه' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    @if($unit->is_active)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full">فعال</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded-full">غیرفعال</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="edit({{ $unit->id }})" class="p-2 text-blue-500 hover:bg-blue-100 rounded-lg transition-all" title="ویرایش">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button onclick="confirm('آیا از حذف این واحد و تغییر سطح فرزندان آن مطمئن هستید؟') || event.stopImmediatePropagation()" 
                                                wire:click="delete({{ $unit->id }})" class="p-2 text-red-400 hover:bg-red-100 rounded-lg transition-all" title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center text-gray-400">
                                    دیتایی جهت نمایش یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    {{ $units->links() }}
                </div>
            </div>
        </div>
    </div>
</div>