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
@if($assign_task_id)
<div class="bg-indigo-50 p-3 rounded mb-3 space-y-2">

    <strong>ارجاع تسک</strong>

    {{-- input جستجو --}}
    <input
        type="text"
        wire:model.live.debounce.300ms="assign_user_search"
        class="border rounded px-2 py-1 w-full"
        placeholder="جستجوی نام کاربر..."
    >

    {{-- لیست نتایج --}}
    @if($assign_user_search)
        <ul class="border rounded bg-white max-h-48 overflow-y-auto">
            @forelse($assignableUsers as $user)
                <li
                    wire:click="$set('assign_user_id', {{ $user->id }})"
                    class="px-3 py-2 hover:bg-indigo-100 cursor-pointer"
                >
                    {{ $user->full_name }}
                </li>
            @empty
                <li class="px-3 py-2 text-gray-500">
                    کاربری یافت نشد
                </li>
            @endforelse
        </ul>
    @endif

    {{-- کاربر انتخاب‌شده --}}
    @if($assign_user_id)
        @php
            $selectedUser = \App\Models\User::find($assign_user_id);
        @endphp

        <div class="text-sm text-green-700">
            انتخاب‌شده:
            <strong>{{ $selectedUser->full_name }}</strong>
        </div>
    @endif

    <div class="flex gap-2 pt-2">
        <button
            wire:click="assignTask"
            class="bg-indigo-600 text-white px-3 py-1 rounded"
        >
            ثبت ارجاع
        </button>

        <button
            wire:click="
                $set('assign_task_id', null);
                $set('assign_user_id', null);
                $set('assign_user_search', '');
            "
            class="text-gray-600"
        >
            انصراف
        </button>
    </div>

</div>
@endif


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
        wire:click="
            $set('assign_task_id', {{ $task->id }})
        "
        class="text-indigo-600"
    >
        ارجاع
    </button>

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
    <button
    wire:click="
        $set(
            'open_activity_task_id',
            {{ $open_activity_task_id === $task->id ? 'null' : $task->id }}
        )
    "
    class="text-gray-600"
>
    تاریخچه
</button>

</td>

                    </tr>
@if($open_activity_task_id === $task->id)
<tr>
    <td colspan="4" class="bg-gray-50 p-3 text-sm">
        <strong>تاریخچه تسک:</strong>

        <ul class="mt-2 space-y-2">
            @foreach($task->activities as $activity)
            @if($activity->action === 'assign')
    @php
        $assignment = $task->assignments
            ->where('created_at', '<=', $activity->created_at)
            ->sortByDesc('created_at')
            ->first();
    @endphp

    @if($assignment)
        <div class="text-sm text-gray-600">
            از:
            {{ $assignment->fromUser?->full_name ?? 'سیستم' }}
            →
            به:
            {{ $assignment->toUser?->full_name }}
        </div>
    @endif
@endif

                <li class="border-b pb-1">
                    <div class="text-gray-700">
                        {{ $activity->created_at->format('Y/m/d H:i') }}
                    </div>

                    <div>
                        {{ match($activity->action) {
                            'assign' => 'ارجاع تسک',
                            'status_change' => 'تغییر وضعیت',
                            default => $activity->action,
                        } }}
                    </div>

                    @if($activity->oldStatus && $activity->newStatus)
                        <div class="text-sm text-gray-600">
                            وضعیت:
                            {{ $activity->oldStatus->title }}
                            →
                            {{ $activity->newStatus->title }}
                        </div>
                    @endif

                    {{-- کاربر --}}
                    <div class="text-sm text-gray-500">
                        توسط:
                        {{ $activity->user?->full_name ?? 'سیستم' }}
                    </div>
                </li>
            @endforeach
        </ul>
    </td>
</tr>
@endif


                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $tasks->links() }}
        </div>
    </div>

</div>
