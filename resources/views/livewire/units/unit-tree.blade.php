<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">نمایش درختی واحدها</h1>

    {{-- فیلتر استان --}}
    <div class="bg-white p-4 rounded shadow">
        <label class="block text-sm font-medium mb-1">استان</label>
        <select wire:model.live="province_id" class="w-full border rounded px-3 py-2">
            <option value="">همه استان‌ها</option>
            @foreach($provinces as $province)
                <option value="{{ $province->id }}">{{ $province->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- وزارت (نمایشی) --}}
    <div class="font-bold text-blue-700">
        وزارت بهداشت
    </div>

    {{-- Tree --}}
    <div class="bg-white p-4 rounded shadow space-y-2">
        @forelse($tree as $node)
            @include('livewire.units.partials.unit-node-array', [
                'node' => $node,
                'level' => 0
            ])
        @empty
            <div class="text-gray-500 text-sm">
                داده‌ای برای نمایش وجود ندارد
            </div>
        @endforelse
    </div>

</div>
