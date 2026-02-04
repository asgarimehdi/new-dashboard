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
                        <th class="p-4">موضوع و محتوا</th>
                        <th class="p-4">زمان ثبت</th>
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
                            <td class="p-4 cursor-pointer hover:bg-indigo-50/30 transition" wire:click="showTicket({{ $ticket->id }})">
    <div class="text-sm font-bold text-gray-800">{{ $ticket->subject }}</div>
    <div class="text-xs text-gray-400 mt-1 line-clamp-1">{{ Str::limit($ticket->content, 50) }}</div>
</td>
                            <td class="p-4 text-xs text-gray-500">{{ jdate($ticket->created_at)->ago() }}</td>
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
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    فایل‌های پیوست
                </h4>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($showingTicket->attachments as $file)
                    <a href="{{ asset('storage/'.$file->path) }}" target="_blank" class="flex items-center p-2 border rounded-lg hover:bg-gray-50 transition">
                        <span class="text-xs text-blue-600 truncate">{{ $file->name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-3">تاریخچه فعالیت‌ها:</h4>
                <div class="border-r-2 border-gray-100 mr-2 space-y-4">
                    @foreach($showingTicket->activities as $activity)
                    <div class="relative pr-6">
                        <div class="absolute right-[-9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-indigo-500"></div>
                        <div class="text-xs font-bold text-gray-700">{{ $activity->action_name ?? $activity->action }}</div>
                        <div class="text-[10px] text-gray-400 mt-1">{{ $activity->description }} - {{ jdate($activity->created_at)->ago() }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-4 border-t bg-gray-50 flex justify-end gap-2">
            <button wire:click="acceptTicket({{ $showingTicket->id }})" class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-bold">تایید و شروع کار</button>
            <button wire:click="closeDetail" class="bg-white border text-gray-600 px-4 py-2 rounded-xl text-sm">بستن</button>
        </div>
    </div>
</div>
@endif
            <div class="p-4">{{ $tickets->links() }}</div>
        </div>
    </div>
</div>