<div class="p-4 max-w-5xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت انواع واحدها</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-3">
        <input
            type="text"
            wire:model.defer="title"
            placeholder="عنوان نوع واحد"
            class="w-full border rounded px-3 py-2"
        >
        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <textarea
            wire:model.defer="description"
            placeholder="توضیحات (اختیاری)"
            class="w-full border rounded px-3 py-2"
        ></textarea>

        <div class="flex gap-2">
            <button
                wire:click="{{ $unitTypeId ? 'update' : 'save' }}"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                {{ $unitTypeId ? 'ویرایش' : 'ثبت' }}
            </button>

            @if($unitTypeId)
                <button wire:click="resetForm" class="bg-gray-300 px-4 py-2 rounded">
                    انصراف
                </button>
            @endif
        </div>
    </div>

    {{-- سرچ --}}
    <input
        type="text"
        wire:model.live.debounce.500ms="search"
        placeholder="جستجو نوع واحد..."
        class="w-full border rounded px-3 py-2"
    >

    {{-- لیست --}}
    <div class="space-y-3">

        {{-- موبایل --}}
        <div class="block md:hidden space-y-3">
            @foreach($types as $type)
                <div class="bg-white p-4 rounded shadow">
                    <div class="font-bold">{{ $type->title }}</div>
                    @if($type->description)
                        <div class="text-sm text-gray-600 mt-1">{{ $type->description }}</div>
                    @endif

                    <div class="mt-2 space-x-2">
                        <button wire:click="edit({{ $type->id }})" class="text-blue-600">ویرایش</button>
                        <button
                            onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                            wire:click="delete({{ $type->id }})"
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
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">عنوان</th>
                        <th class="p-3">توضیحات</th>
                        <th class="p-3">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $type)
                        <tr class="border-t">
                            <td class="p-3">{{ $type->id }}</td>
                            <td class="p-3">{{ $type->title }}</td>
                            <td class="p-3">{{ $type->description }}</td>
                            <td class="p-3 space-x-3">
                                <button wire:click="edit({{ $type->id }})" class="text-blue-600">ویرایش</button>
                                <button
                                    onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $type->id }})"
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

        {{ $types->links() }}
    </div>

</div>
