@php
    $colors = [
        0 => 'text-blue-700',
        1 => 'text-green-700',
        2 => 'text-purple-700',
        3 => 'text-gray-700',
        4 => 'text-orange-700',
    ];

    $hasChildren = !empty($node['children']);
@endphp

<div
    class="mt-2"
    style="margin-right: {{ $level * 20 }}px"
    x-data="{ open: true }"
>

    {{-- ردیف واحد --}}
    <div class="flex items-center gap-2 cursor-pointer select-none"
         @click="open = !open"
    >

        {{-- آیکن + / - --}}
        @if($hasChildren)
            <span class="text-xs w-4 inline-block text-center">
                <span x-show="open">➖</span>
                <span x-show="!open">➕</span>
            </span>
        @else
            <span class="text-xs w-4 inline-block text-center text-gray-400">
                •
            </span>
        @endif

        {{-- نام واحد --}}
        <span class="font-semibold {{ $colors[$level] ?? 'text-gray-700' }}">
            {{ $node['model']->name }}
        </span>

        {{-- نوع واحد --}}
        <span class="text-xs text-gray-500">
            ({{ $node['model']->type->title }})
        </span>

        {{-- وضعیت --}}
        @if(!$node['model']->is_active)
            <span class="text-xs text-red-600">
                غیرفعال
            </span>
        @endif
    </div>

    {{-- فرزندان --}}
    @if($hasChildren)
        <div
            x-show="open"
            x-transition
            class="mt-1"
        >
            @foreach($node['children'] as $child)
                @include('livewire.units.partials.unit-node-array', [
                    'node' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif

</div>
