<div class="p-4 md:p-8 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">مدیریت سلسله‌مراتب انواع واحدها</h1>
            <p class="text-sm text-gray-500 mt-1">تعیین کنید که هر نوع واحد، مجاز به داشتن چه زیرمجموعه‌هایی است.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            {{-- ستون سمت راست: لیست واحدها --}}
            <div class="md:col-span-4 space-y-2">
                <label class="block text-sm font-bold text-gray-700 mb-3">۱. انتخاب واحد سطح بالا (والد)</label>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                        @foreach($unitTypes as $type)
                            <button 
                                wire:click="selectParent({{ $type->id }})"
                                class="w-full text-right px-4 py-3 text-sm transition flex justify-between items-center {{ $selectedParentId == $type->id ? 'bg-blue-600 text-white' : 'hover:bg-gray-50 text-gray-700' }}"
                            >
                                <span>{{ $type->title }}</span>
                                @if($selectedParentId == $type->id)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ستون سمت چپ: انتخاب فرزندان --}}
            <div class="md:col-span-8">
                <label class="block text-sm font-bold text-gray-700 mb-3">۲. تعیین زیرمجموعه‌های مجاز</label>
                
                @if($selectedParentId)
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden flex flex-col h-full transition-all">
                        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-medium">زیرمجموعه‌های مجاز برای: <strong class="text-blue-700">{{ $unitTypes->find($selectedParentId)->title }}</strong></span>
                            <span class="text-xs text-gray-400">{{ count($selectedChildrenIds) }} مورد انتخاب شده</span>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto">
                            @foreach($unitTypes as $child)
                                {{-- جلوگیری از انتخاب خود واحد به عنوان فرزند خودش (Self-loop protection) --}}
                                @if($child->id != $selectedParentId)
                                    <label class="relative flex items-center p-3 rounded-lg border border-gray-100 hover:bg-blue-50 cursor-pointer transition group">
                                        <input type="checkbox" 
                                               wire:model="selectedChildrenIds" 
                                               value="{{ $child->id }}"
                                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="mr-3 text-sm text-gray-700 group-hover:text-blue-800">{{ $child->title }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                            <button 
                                wire:click="save" 
                                class="bg-green-600 hover:bg-green-700 text-white px-8 py-2 rounded-lg font-bold shadow-lg transition-all flex items-center"
                            >
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                ذخیره تغییرات سلسله‌مراتب
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl border-2 border-dashed border-gray-200 h-64 flex flex-center items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-gray-400">لطفاً ابتدا یک واحد را از لیست سمت راست انتخاب کنید.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>