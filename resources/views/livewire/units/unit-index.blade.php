<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت واحدها</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-3">

        <input wire:model.defer="name" class="w-full border rounded px-3 py-2" placeholder="نام واحد">

        <select wire:model.live="province_id" class="w-full border rounded px-3 py-2">
            <option value="">انتخاب استان</option>
            @foreach($provinces as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>

        <select wire:model.defer="city_id" class="w-full border rounded px-3 py-2">
            <option value="">انتخاب شهر</option>
            @foreach($cities as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>

        <select wire:model.defer="unit_type_id" class="w-full border rounded px-3 py-2">
            <option value="">نوع واحد</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}">{{ $t->title }}</option>
            @endforeach
        </select>

        <select wire:model.defer="parent_id" class="w-full border rounded px-3 py-2">
            <option value="">واحد بالادست (اختیاری)</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
            @endforeach
        </select>

        <label class="flex items-center gap-2">
            <input type="checkbox" wire:model.defer="is_active">
            فعال
        </label>

        <div class="flex gap-2">
            <button wire:click="{{ $unitId ? 'update' : 'save' }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                {{ $unitId ? 'ویرایش' : 'ثبت' }}
            </button>

            @if($unitId)
                <button wire:click="resetForm" class="bg-gray-300 px-4 py-2 rounded">
                    انصراف
                </button>
            @endif
        </div>
    </div>

    {{-- سرچ --}}
    <input wire:model.live.debounce.500ms="search" class="w-full border rounded px-3 py-2" placeholder="جستجو واحد...">

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
                        <td class="p-3">{{ $unit->city->name }}</td>
                        <td class="p-3">{{ $unit->parent?->name ?? '-' }}</td>
                        <td class="p-3">
                            <span class="{{ $unit->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $unit->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="p-3 space-x-2">
                            <button wire:click="edit({{ $unit->id }})" class="text-blue-600">ویرایش</button>
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

        {{ $units->links() }}
    </div>

</div>
