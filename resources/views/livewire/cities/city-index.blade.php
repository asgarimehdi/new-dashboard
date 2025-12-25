<div class="p-4 max-w-5xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت شهرها</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-3">
        <select wire:model.defer="province_id" class="w-full border rounded px-3 py-2">
            <option value="">انتخاب استان</option>
            @foreach($provinces as $province)
                <option value="{{ $province->id }}">{{ $province->name }}</option>
            @endforeach
        </select>
        @error('province_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <input
            type="text"
            wire:model.defer="name"
            placeholder="نام شهر"
            class="w-full border rounded px-3 py-2"
        >
        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <div class="flex gap-2">
            <button
                wire:click="{{ $cityId ? 'update' : 'save' }}"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                {{ $cityId ? 'ویرایش' : 'ثبت' }}
            </button>

            @if($cityId)
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
        placeholder="جستجو شهر یا استان..."
        class="w-full border rounded px-3 py-2"
    >

    {{-- لیست --}}
    <div class="space-y-3">

        {{-- موبایل --}}
        <div class="block md:hidden space-y-3">
            @foreach($cities as $city)
                <div class="bg-white p-4 rounded shadow">
                    <div class="font-bold">{{ $city->name }}</div>
                    <div class="text-sm text-gray-600">{{ $city->province->name }}</div>

                    <div class="mt-2 space-x-2">
                        <button wire:click="edit({{ $city->id }})" class="text-blue-600">ویرایش</button>
                        <button
                            onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                            wire:click="delete({{ $city->id }})"
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
                        <th class="p-3">شهر</th>
                        <th class="p-3">استان</th>
                        <th class="p-3">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cities as $city)
                        <tr class="border-t">
                            <td class="p-3">{{ $city->id }}</td>
                            <td class="p-3">{{ $city->name }}</td>
                            <td class="p-3">{{ $city->province->name }}</td>
                            <td class="p-3 space-x-3">
                                <button wire:click="edit({{ $city->id }})" class="text-blue-600">ویرایش</button>
                                <button
                                    onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $city->id }})"
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

        {{ $cities->links() }}
    </div>

</div>
