@php
    $hasChildren = !empty($node['children']);
    $isActive = $node['model']->is_active;
@endphp

<div class="relative" x-data="{ open: @json($level < 1) }"> {{-- فقط سطح اول باز باشد --}}
    
    {{-- خط عمودی راهنما --}}
    @if($level > 0)
        <div class="absolute right-[-15px] top-[-10px] bottom-0 w-px bg-gray-200"></div>
    @endif

    <div class="group flex items-center py-2 relative">
        
        {{-- خط افقی اتصال --}}
        @if($level > 0)
            <div class="absolute right-[-15px] top-1/2 w-4 h-px bg-gray-200"></div>
        @endif

        <div class="flex items-center w-full bg-transparent hover:bg-gray-50 p-1 rounded-lg transition-colors duration-150 cursor-pointer"
             @click="open = !open">
            
            {{-- دکمه باز و بسته کردن --}}
            <div class="z-10 flex items-center justify-center w-6 h-6 rounded-md {{ $hasChildren ? 'bg-white border shadow-sm' : '' }}">
                @if($hasChildren)
                    <template x-if="open">
                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                    </template>
                    <template x-if="!open">
                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    </template>
                @else
                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                @endif
            </div>

            <div class="mr-3 flex flex-wrap items-center gap-2">
                {{-- نام واحد --}}
                <span class="text-sm font-bold {{ $isActive ? 'text-gray-700' : 'text-gray-400 line-through' }}">
                    {{ $node['model']->name }}
                </span>

                {{-- لیبل نوع واحد --}}
                <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-500 rounded uppercase tracking-tighter">
                    {{ $node['model']->type->title }}
                </span>

                {{-- وضعیت --}}
                @if(!$isActive)
                    <span class="flex items-center gap-1 text-[10px] text-red-500 bg-red-50 px-1.5 py-0.5 rounded">
                        <span class="w-1 h-1 bg-red-500 rounded-full animate-pulse"></span>
                        غیرفعال
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- رندر زیرمجموعه‌ها --}}
    @if($hasChildren)
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             class="mr-8 border-r-0">
            @foreach($node['children'] as $child)
                @include('livewire.units.partials.unit-node-array', [
                    'node' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div>