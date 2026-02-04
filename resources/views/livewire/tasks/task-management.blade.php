<div class="p-6 bg-gray-50 min-h-screen" x-data="{ showCreateModal: false }">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-black text-gray-800 flex items-center gap-3">
                <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
                مدیریت وظایف اجرایی
            </h1>
            <button @click="showCreateModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center gap-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                تسک جدید
            </button>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex gap-2 p-1 bg-gray-100 rounded-lg">
                <button wire:click="$set('viewMode', 'inbox')" class="px-4 py-1.5 rounded-md text-sm {{ $viewMode == 'inbox' ? 'bg-white shadow text-indigo-600 font-bold' : 'text-gray-500' }}">ورودی</button>
                <button wire:click="$set('viewMode', 'sent')" class="px-4 py-1.5 rounded-md text-sm {{ $viewMode == 'sent' ? 'bg-white shadow text-indigo-600 font-bold' : 'text-gray-500' }}">ارجاعی من</button>
                <button wire:click="$set('viewMode', 'all')" class="px-4 py-1.5 rounded-md text-sm {{ $viewMode == 'all' ? 'bg-white shadow text-indigo-600 font-bold' : 'text-gray-500' }}">همه</button>
            </div>
            <input type="text" wire:model.live="search" placeholder="جستجوی موضوع یا کد..." class="border-gray-200 rounded-xl text-sm w-full md:w-64 focus:ring-indigo-500">
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="p-4 text-gray-600 font-bold text-sm">موضوع</th>
                        <th class="p-4 text-gray-600 font-bold text-sm">اولویت</th>
                        <th class="p-4 text-gray-600 font-bold text-sm">وضعیت</th>
                        <th class="p-4 text-gray-600 font-bold text-sm">آخرین بروزرسانی</th>
                        <th class="p-4 text-gray-600 font-bold text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="font-bold text-gray-800">{{ $task->subject }}</div>
                                <div class="text-[10px] text-gray-400 mt-1">کد: {{ $task->ticket_code }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] {{ $task->priority == 'high' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $task->priority }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                            {{ $task->status_name }} 
                            @if($task->taskStatus) 
                            {{ $task->taskStatus->name }}
                              @endif
                            </td>
                            <td class="p-4 text-xs text-gray-400">{{ jdate($task->updated_at)->ago() }}</td>
                            <td class="p-4 text-left">
                                <button class="text-indigo-600 hover:underline text-sm font-bold">جزئیات و گفتگو</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-400">هیچ تسکی یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $tasks->links() }}</div>
        </div>

        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.away="showCreateModal = false">
                <h3 class="text-xl font-bold mb-6">ایجاد تسک مستقیم</h3>
                <div class="space-y-4">
                    <input type="text" wire:model="subject" placeholder="عنوان تسک" class="w-full border-gray-200 rounded-xl">
                    <textarea wire:model="content" placeholder="توضیحات کامل..." rows="4" class="w-full border-gray-200 rounded-xl"></textarea>
                    <div class="grid grid-cols-2 gap-4">
                       <select wire:model="unit_id" class="border-gray-200 rounded-xl text-sm">
    <option value="">انتخاب واحد مسئول...</option>
    @foreach($units as $unit)
        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
    @endforeach
</select>
                        <select wire:model="priority" class="border-gray-200 rounded-xl text-sm">
                            <option value="normal">عادی</option>
                            <option value="high">فوری</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 mt-8">
                    <button wire:click="createDirectTask" @click="showCreateModal = false" class="flex-1 bg-indigo-600 text-white py-2 rounded-xl font-bold">ثبت تسک</button>
                    <button @click="showCreateModal = false" class="flex-1 bg-gray-100 text-gray-600 py-2 rounded-xl font-bold">انصراف</button>
                </div>
            </div>
        </div>
    </div>
</div>