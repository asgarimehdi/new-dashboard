<div class="ml-4 mt-2 border-r pr-4">

    <div class="flex items-center gap-2">
        <span class="font-semibold">
            {{ $unit->name }}
        </span>

        <span class="text-xs text-gray-500">
            ({{ $unit->type->title }})
        </span>

        @if(!$unit->is_active)
            <span class="text-xs text-red-600">غیرفعال</span>
        @endif
    </div>

    @if($unit->children->isNotEmpty())
        <div class="mt-2">
            @foreach($unit->children as $child)
                @include('livewire.units.partials.unit-node', ['unit' => $child])
            @endforeach
        </div>
    @endif

</div>
