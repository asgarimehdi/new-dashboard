<div class="p-4 md:p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h1 class="text-2xl font-extrabold text-gray-800">ساختار درختی واحدها</h1>
            <div class="text-sm font-medium px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                وزارت بهداشت، درمان و آموزش پزشکی
            </div>
        </div>

        {{-- بخش فیلتر --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">فیلتر بر اساس استان</label>
                <select wire:model.live="province_id" class="w-full border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm transition shadow-sm">
                    <option value="">همه استان‌ها (کل کشور)</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- کانتینر درخت --}}
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            @if(count($tree) > 0)
                <div class="relative">
                    @foreach($tree as $node)
                        @include('livewire.units.partials.unit-node-array', [
                            'node' => $node,
                            'level' => 0
                        ])
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center">
                    <div class="text-gray-300 mb-3 text-5xl">🔍</div>
                    <div class="text-gray-500 font-medium">هیچ واحدی با این مشخصات یافت نشد.</div>
                </div>
            @endif
        </div>
    </div>
</div>