<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">تیکت‌های ورودی واحد</h2>
            <input type="text" wire:model.live="search" placeholder="جستجوی کد یا موضوع..." class="border-gray-200 rounded-xl text-sm w-64">
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="p-4">کد / فرستنده</th>
                        <th class="p-4"> اولویت </th>
                        <th class="p-4">موضوع و محتوا</th>
                        <th class="p-4">زمان ثبت</th>
                        <th class="p-4"> جزئیات</th>
                        <th class="p-4 text-left">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <span class="font-mono text-indigo-600 block text-xs">#{{ $ticket->ticket_code }}</span>
                                <span class="text-sm font-bold text-gray-700">{{ $ticket->creator?->name ?? 'کاربر سیستم' }}</span>
                            </td>
                            <td class="p-4 text-center">
        @php
            $priorityColors = [
                'urgent' => 'bg-red-100 text-red-700 border-red-200',
                'normal' => 'bg-blue-100 text-blue-700 border-blue-200',
                'low'    => 'bg-gray-100 text-gray-700 border-gray-200',
            ];
            $priorityLabels = ['urgent' => 'فوری', 'normal' => 'معمولی', 'low' => 'کم‌اهمیت'];
        @endphp
        <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $priorityColors[$ticket->priority] ?? $priorityColors['low'] }}">
            {{ $priorityLabels[$ticket->priority] ?? 'نامشخص' }}
        </span>
    </td>
                            <td class="p-4  hover:bg-indigo-50/30 transition">
    <div class="text-sm font-bold text-gray-800">{{ $ticket->subject }}</div>
    <div class="text-xs text-gray-400 mt-1 line-clamp-1">{{ Str::limit($ticket->content, 50) }}</div>
</td>
                            <td class="p-4 text-xs text-gray-500">{{ jdate($ticket->created_at)->ago() }}</td>
                            <td>
                                <button wire:click="showTicket({{ $ticket->id }})" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="مشاهده جزئیات">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Path.trunc(15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </td>
                            <td class="p-4 text-left flex items-center justify-end space-x-reverse space-x-2">

    <select wire:model="selectedUnit.{{ $ticket->id }}" 
        class="text-xs border-gray-200 rounded-lg p-1.5 bg-gray-50 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition w-40">
    <option value="">ارجاع به واحد دیگر...</option>
    @foreach(\App\Models\Unit::where('can_receive_tickets', true) // نام درست ستون
                             ->where('id', '!=', $ticket->unit_id)
                             ->get() as $unit)
        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
    @endforeach
</select>

   <button wire:click="forwardTicket({{ $ticket->id }})" 
                class="bg-indigo-50 text-indigo-600 p-2 rounded-lg hover:bg-indigo-100 title="ارجاع به واحد انتخاب شده">
           <!--  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Math.trunc(8 7l4 4m0 0l-4 4m4-4H3" />
            </svg> -->
            ارجاع
        </button>

       

        <button wire:click="acceptTicket({{ $ticket->id }})" class="bg-green-50 text-green-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-100">
            تایید
        </button>
        
        <button onclick="confirm('رد تیکت؟') || event.stopImmediatePropagation()" 
                wire:click="rejectTicket({{ $ticket->id }})" 
                class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100">
            رد
        </button>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center text-gray-400">تیکت جدیدی برای واحد شما ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($showingTicket)
<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center bg-gray-50 rounded-t-2xl">
            <div>
                <h3 class="text-lg font-bold text-gray-800">{{ $showingTicket->subject }}</h3>
                <span class="text-xs text-gray-500 font-mono">#{{ $showingTicket->ticket_code }}</span>
            </div>
            <button wire:click="closeDetail" class="text-gray-400 hover:text-red-500 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 space-y-6 text-right" dir="rtl">
            <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                <h4 class="text-sm font-bold text-indigo-900 mb-2">متن تیکت:</h4>
                <p class="text-gray-700 leading-relaxed text-sm">{{ $showingTicket->content }}</p>
            </div>

            @if($showingTicket->attachments->count() > 0)
           <div class="mt-6">
    <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
        مستندات و پیوست‌ها ({{ $showingTicket->attachments->count() }})
    </h4>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @forelse($showingTicket->attachments as $file)
            @php
                $extension = pathinfo($file->path, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif']);
            @endphp
            
            <div class="group relative flex items-center p-3 border border-gray-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/30 transition shadow-sm">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg {{ $isImage ? 'bg-orange-50' : 'bg-blue-50' }}">
                    @if($isImage)
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    @else
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    @endif
                </div>
                
                <div class="mr-3 flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-700 truncate" title="{{ $file->name }}">
                        {{ $file->name }}
                    </p>
                    <p class="text-[10px] text-gray-400 uppercase">{{ $extension }}</p>
                </div>

                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 p-1 bg-white shadow-sm border rounded-md text-indigo-600 hover:bg-indigo-600 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </a>
           
            </div>
        @empty
            <div class="col-span-2 text-center py-4 bg-gray-50 rounded-xl text-gray-400 text-xs border border-dashed">
                پیوستی برای این تیکت ثبت نشده است.
            </div>
        @endforelse
    </div>
</div>
            @endif

           <div class="relative space-y-4 before:absolute before:right-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
    
    {{-- ۱. نمایش فعالیت‌های ثبت شده در دیتابیس --}}
 @foreach($showingTicket->activities->sortByDesc('created_at') as $activity)
    <div class="relative pr-8 mb-4">
        <div class="absolute right-0 top-1 w-5 h-5 rounded-full bg-white border-2 
            {{ $activity->action == 'created' ? 'border-green-500' : 'border-blue-500' }} 
            flex items-center justify-center z-10 text-[10px]">
            {{ $activity->action == 'created' ? '✨' : '📨' }}
        </div>

        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-bold text-gray-900">{{ $activity->user->name }}</span>
                <span class="text-[10px] text-gray-400" dir="ltr">
                    {{ jdate($activity->created_at)->format('H:i - Y/m/d') }}
                </span>
            </div>
            <p class="text-xs text-gray-600">{{ $activity->description }}</p>
            
            @if($activity->to_unit_id)
                <div class="mt-2 text-[10px] bg-blue-50 text-blue-700 px-2 py-1 rounded-lg inline-block">
                    ارجاع به واحد: {{ $activity->toUnit->name ?? 'نامشخص' }}
                </div>
            @endif
        </div>
    </div>
@endforeach
</div>
        </div>

        <div class="p-4 border-t bg-gray-50 flex justify-end gap-2">
            <!-- <button wire:click="acceptTicket({{ $showingTicket->id }})" class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-bold">تایید و شروع کار</button> -->
            <button wire:click="closeDetail" class="bg-white border text-gray-600 px-4 py-2 rounded-xl text-sm">بستن</button>
        </div>
    </div>
</div>
@endif
            <div class="p-4">{{ $tickets->links() }}</div>
        </div>
    </div>
</div>