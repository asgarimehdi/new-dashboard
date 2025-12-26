<div class="p-4 max-w-6xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">مدیریت تسک‌ها</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- فرم --}}
    <div class="bg-white p-4 rounded shadow space-y-4">

        <input
            type="text"
            wire:model="title"
            class="w-full border rounded px-3 py-2"
            placeholder="عنوان تسک"
        >
        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <textarea
            wire:model="description"
            class="w-full border rounded px-3 py-2"
            placeholder="توضیحات (اختیاری)"
        ></textarea>

        <select
            wire:model="unit_id"
            class="w-full border rounded px-3 py-2"
        >
            <option value="">انتخاب واحد</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
            @endforeach
        </select>
        @error('unit_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <div class="flex gap-2">
            <button
                wire:click="save"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                {{ $taskId ? 'ویرایش تسک' : 'ثبت تسک' }}
            </button>

            @if($taskId)
                <button
                    wire:click="resetForm"
                    class="bg-gray-300 px-4 py-2 rounded"
                >
                    انصراف
                </button>
            @endif
        </div>
    </div>
<div class="bg-white p-4 rounded shadow space-y-2">

    <select wire:model="assign_task_id" class="w-full border rounded px-3 py-2">
        <option value="">انتخاب تسک</option>
        @foreach($tasks as $task)
            <option value="{{ $task->id }}">{{ $task->title }}</option>
        @endforeach
    </select>

    <select wire:model="assign_user_id" class="w-full border rounded px-3 py-2">
        <option value="">انتخاب کاربر</option>
        @foreach(\App\Models\User::where('is_active', true)->get() as $user)
            <option value="{{ $user->id }}">{{ $user->full_name }}</option>
        @endforeach
    </select>

    <button
        wire:click="assignTask"
        class="bg-indigo-600 text-white px-4 py-2 rounded"
    >
        ارجاع تسک
    </button>

</div>

    {{-- سرچ --}}
    <input
        type="text"
        wire:model.live.debounce.500ms="search"
        class="w-full border rounded px-3 py-2"
        placeholder="جستجو عنوان یا واحد..."
    >

    {{-- جدول --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">عنوان</th>
                    <th class="p-3">واحد</th>
                    <th class="p-3">وضعیت</th>
                    <th class="p-3">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr class="border-t">
                        <td class="p-3">{{ $task->title }}</td>
                        <td class="p-3">{{ $task->unit->name }}</td>
                        <td class="p-3">
    <select
        wire:change="changeStatus({{ $task->id }}, $event.target.value)"
        class="border rounded px-2 py-1 text-sm"
    >
        @foreach(\App\Models\TaskStatus::all() as $status)
            <option
                value="{{ $status->id }}"
                @selected($task->task_status_id == $status->id)
            >
                {{ $status->title }}
            </option>
        @endforeach
    </select>
</td>

                        <td class="p-3 space-x-2">
                            <button
                                wire:click="edit({{ $task->id }})"
                                class="text-blue-600"
                            >
                                ویرایش
                            </button>
                            <button
                                onclick="confirm('حذف شود؟') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $task->id }})"
                                class="text-red-600"
                            >
                                حذف
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $tasks->links() }}
        </div>
    </div>

</div>
