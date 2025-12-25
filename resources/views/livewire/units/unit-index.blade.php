<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت واحدها</h1>

    {{-- پیام موفقیت --}}
    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-4">

        {{-- نام واحد --}}
        <div>
            <label class="block text-sm font-medium mb-1">نام واحد</label>
            <input
                type="text"
                wire:model.defer="name"
                class="w-full border rounded px-3 py-2"
            >
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- نوع واحد --}}
        <div>
            <label class="block text-sm font-medium mb-1">نوع واحد</label>
            <select wire:model.live="unit_type_id" class="w-full border rounded px-3 py-2">
                <option value="">انتخاب نوع واحد</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->title }}</option>
                @endforeach
            </select>
            @error('unit_type_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- استان و شهر (به‌جز واحد ملی) --}}
        @if(!$this->isNationalUnit())

            <div>
                <label class="block text-sm font-medium mb-1">استان</label>
                <select wire:model.live="province_id" class="w-full border rounded px-3 py-2">
                    <option value="">انتخاب استان</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                    @endforeach
                </select>
                @error('province_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">شهر</label>
                <select wire:model.defer="city_id" class="w-full border rounded px-3 py-2">
                    <option value="">انتخاب شهر</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
                @error('city_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

        @else
            <div class="text-sm text-gray-600">
                این واحد در سطح ملی است و وابسته به استان یا شهر نیست.
            </div>
        @endif

        {{-- واحد بالادست --}}
        @if($unit_type_id)

            @if($this->requiresParent)

                <div>
                    <label class="block text-sm font-medium mb-1">
                        واحد بالادست <span class="text-red-600">*</span>
                    </label>

                    @if($parents->isNotEmpty())
                        <select wire:model.defer="parent_id" class="w-full border rounded px-3 py-2">
                            <option value="">انتخاب واحد بالادست</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">
                                    {{ $parent->name }} ({{ $parent->type->title }})
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="text-red-600 text-sm">
                            برای این نوع واحد هنوز بالادست مجاز تعریف نشده است.
                        </div>
                    @endif

                    @error('parent_id')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

            @else
                <div class="text-sm text-gray-600">
                    این نوع واحد در سطح ریشه قرار دارد.
                </div>
            @endif

        @endif

        {{-- وضعیت --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model.defer="is_active" id="is_active">
            <label for="is_active" class="text-sm">واحد فعال باشد</label>
        </div>

        {{-- دکمه‌ها --}}
        <div class="flex gap-2">
            <button
                wire:click="{{ $unitId ? 'update' : 'save' }}"
                @if($this->requiresParent && $parents->isEmpty()) disabled @endif
                class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50"
            >
                {{ $unitId ? 'ویرایش' : 'ثبت' }}
            </button>

            @if($unitId)
                <button
                    wire:click="resetForm"
                    class="bg-gray-300 px-4 py-2 rounded"
                >
                    انصراف
                </button>
            @endif
        </div>

    </div>

    {{-- جستجو --}}
    <input
        type="text"
        wire:model.live.debounce.500ms="search"
        class="w-full border rounded px-3 py-2"
        placeholder="جستجو نام واحد..."
    >

    {{-- لیست --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">نام</th>
                    <th class="p-3">نوع</th>
                    <th class="p-3">شهر</th>
                    <th class="p-3">بالادست</th>
                    <th class="p-3">وضعیت</th>
                    <th class="p-3">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                    <tr class="border-t">
                        <td class="p-3">{{ $unit->name }}</td>
                        <td class="p-3">{{ $unit->type->title }}</td>
                        <td class="p-3">{{ $unit->city?->name ?? 'ملی' }}</td>
                        <td class="p-3">{{ $unit->parent?->name ?? '-' }}</td>
                        <td class="p-3">
                            <span class="{{ $unit->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $unit->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="p-3 space-x-2">
                            <button wire:click="edit({{ $unit->id }})" class="text-blue-600">
                                ویرایش
                            </button>
                            <button
                                onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $unit->id }})"
                                class="text-red-600"
                            >
                                حذف
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $units->links() }}
        </div>
    </div>

</div>
