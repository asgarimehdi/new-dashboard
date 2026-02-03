<div class="p-4 max-w-6xl mx-auto">
    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h2 class="font-bold text-gray-700">تیکت‌های ورودی</h2>
            <div class="text-xs text-gray-500">مدیریت درخواست‌های واحد</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="p-3">کد / موضوع</th>
                        <th class="p-3">فرستنده</th>
                        <th class="p-3">پیوست</th>
                        <th class="p-3 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="p-3">
                                <div class="font-bold text-blue-700">#{{ $ticket->ticket_code }}</div>
                                <div class="text-gray-900 font-medium">{{ $ticket->subject }}</div>
                                <div class="text-[11px] text-gray-400 mt-1 italic line-clamp-1">{{ $ticket->content }}</div>
                            </td>
                            <td class="p-3">
                                <span class="bg-gray-100 px-2 py-1 rounded text-[11px]">کاربر سیستم</span>
                                <div class="text-[10px] mt-1 {{ $ticket->priority == 'urgent' ? 'text-red-500' : 'text-gray-400' }}">
                                    اولویت: {{ $ticket->priority }}
                                </div>
                            </td>
                            <td class="p-3">
                                @if($ticket->attachments->count() > 0)
                                    <div class="flex gap-1">
                                        @foreach($ticket->attachments as $file)
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" title="{{ $file->file_name }}">
                                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.414a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="p-3">
                               <div class="flex items-center gap-2">
    <button wire:click="acceptTicket({{ $ticket->id }})" 
            wire:loading.attr="disabled"
            class="bg-green-600 text-white px-3 py-1.5 rounded-md text-[11px] hover:bg-green-700 transition disabled:opacity-50">
        قبول
    </button>
    
    <button @click="$dispatch('open-modal-assign'); $wire.set('selectedTicketId', {{ $ticket->id }})" 
            class="bg-indigo-600 text-white px-3 py-1.5 rounded-md text-[11px] hover:bg-indigo-700 transition">
        ارجاع...
    </button>

    <button onclick="confirm('از رد این درخواست مطمئن هستید؟') || event.stopImmediatePropagation()"
            wire:click="rejectTicket({{ $ticket->id }})" 
            class="bg-white text-red-600 border border-red-200 px-3 py-1.5 rounded-md text-[11px] hover:bg-red-50 transition">
        رد کردن
    </button>
</div>
<div x-data="{ open: false }" 
     @open-modal-assign.window="open = true" 
     @close-modal.window="open = false"
     x-show="open" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
    
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl" @click.away="open = false">
        <h4 class="font-bold mb-4">انتخاب کارشناس جهت ارجاع</h4>
        <input type="text" wire:model.live="assign_user_search" placeholder="جستجوی نام کارشناس..." 
               class="w-full border rounded-lg p-2 text-sm mb-4">
        
        <div class="max-h-48 overflow-y-auto space-y-2">
            @foreach($assignableUsers as $user)
               <button wire:click="assignToUser({{ $user->id }})" @click="open = false"
        class="w-full text-right p-2.5 hover:bg-indigo-50 rounded-lg border border-gray-50 text-sm flex justify-between items-center group transition">
    <span>{{ $user->full_name }}</span>
    <span class="text-indigo-500 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition">انتخاب و ارجاع</span>
</button>
            @endforeach
        </div>
        
        <button @click="open = false" class="mt-4 text-xs text-gray-400 w-full text-center hover:text-gray-600">انصراف</button>
    </div>
</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50 border-t">
            {{ $tickets->links() }}
        </div>
    </div>
</div>