<div class="p-4 max-w-5xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت استان‌ها</h1>

    {{-- پیام موفقیت --}}
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-3">
        <input
            type="text"
            wire:model.defer="name"
            placeholder="نام استان"
            class="w-full border rounded px-3 py-2"
        >
        @error('name')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror

        <div class="flex gap-2">
            <button
                wire:click="{{ $provinceId ? 'update' : 'save' }}"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                {{ $provinceId ? 'ویرایش' : 'ثبت' }}
            </button>

            @if($provinceId)
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
        placeholder="جستجو استان..."
        class="w-full border rounded px-3 py-2"
    >

    {{-- لیست --}}
    <div class="space-y-3">

        {{-- موبایل --}}
        <div class="block md:hidden space-y-3">
            @foreach($provinces as $province)
                <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                    <span class="font-medium">{{ $province->name }}</span>
                    <div class="space-x-2">
                        <button wire:click="edit({{ $province->id }})" class="text-blue-600">
                            ویرایش
                        </button>
                        <button
                            onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                            wire:click="delete({{ $province->id }})"
                            class="text-red-600"
                        >
                            حذف
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- دسکتاپ --}}
        <div class="hidden md:block bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">نام استان</th>
                        <th class="p-3">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($provinces as $province)
                        <tr class="border-t">
                            <td class="p-3">{{ $province->id }}</td>
                            <td class="p-3">{{ $province->name }}</td>
                            <td class="p-3 space-x-3">
                                <button wire:click="edit({{ $province->id }})" class="text-blue-600">
                                    ویرایش
                                </button>
                                <button
                                    onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $province->id }})"
                                    class="text-red-600"
                                >
                                    حذف
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div>
            {{ $provinces->links() }}
        </div>

    </div>

</div>
