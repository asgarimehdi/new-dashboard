<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت کاربران</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-4">

        <input
            type="text"
            wire:model="full_name"
            class="w-full border rounded px-3 py-2"
            placeholder="نام و نام خانوادگی"
        >
        @error('full_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <input
            type="text"
            wire:model="national_code"
            class="w-full border rounded px-3 py-2"
            placeholder="کد ملی"
        >
        @error('national_code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <select
            wire:model="unit_id"
            class="w-full border rounded px-3 py-2"
        >
            <option value="">انتخاب واحد</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
            @endforeach
        </select>
        @error('unit_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="is_active">
            فعال
        </label>

        <div class="flex gap-2">
            <button
                wire:click="save"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                {{ $userId ? 'ویرایش کاربر' : 'ثبت کاربر' }}
            </button>

            @if($userId)
                <button
                    wire:click="resetForm"
                    class="bg-gray-300 px-4 py-2 rounded"
                >
                    انصراف
                </button>
            @endif
        </div>
    </div>

    {{-- سرچ --}}
    <input
        type="text"
        wire:model.live.debounce.500ms="search"
        class="w-full border rounded px-3 py-2"
        placeholder="جستجو نام، کد ملی یا واحد..."
    >

    {{-- جدول --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">نام</th>
                    <th class="p-3">کد ملی</th>
                    <th class="p-3">واحد</th>
                    <th class="p-3">وضعیت</th>
                    <th class="p-3">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-t">
                        <td class="p-3">{{ $user->full_name }}</td>
                        <td class="p-3">{{ $user->national_code }}</td>
                        <td class="p-3">{{ $user->unit?->name }}</td>
                        <td class="p-3">
                            <span class="{{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="p-3 space-x-2">
                            <button
                                wire:click="edit({{ $user->id }})"
                                class="text-blue-600"
                            >
                                ویرایش
                            </button>
                            <button
                                onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $user->id }})"
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
            {{ $users->links() }}
        </div>
    </div>

</div>
