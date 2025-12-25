<div class="p-6 max-w-4xl mx-auto">

    <h1 class="text-xl font-bold mb-4">مدیریت استان‌ها</h1>

    @if (session()->has('success'))
        <div class="mb-3 text-green-600">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded shadow mb-6">
        <input
            type="text"
            wire:model.defer="name"
            placeholder="نام استان"
            class="w-full border rounded px-3 py-2 mb-3"
        >

        <button
            wire:click="{{ $provinceId ? 'update' : 'save' }}"
            class="bg-blue-600 text-white px-4 py-2 rounded"
        >
            {{ $provinceId ? 'ویرایش' : 'ثبت' }}
        </button>
    </div>

    <div class="bg-white rounded shadow">
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
                        <td class="p-3 space-x-2">
                            <button
                                wire:click="edit({{ $province->id }})"
                                class="text-blue-600"
                            >
                                ویرایش
                            </button>

                            <button
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

</div>
