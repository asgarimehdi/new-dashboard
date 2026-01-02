<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Unit;
use App\Models\TaskStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\TaskActivity;
use App\Models\TaskAssignment;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
#[Layout('components.layouts.app')]
class TaskIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $taskId = null;
    public $open_activity_task_id = null;
    public $assign_user_search = '';
    public $task_view = 'all'; 
    // all | inbox | sent

    public $title = '';
    public $description = '';
    public $unit_id = '';
    public $task_status_id = null;

    public $search = '';
    public $assign_task_id = null;
    public $assign_user_id = null;
    public $priority = 'normal';
    public $due_date;
    public $filter_status = '';
    public $filter_priority = '';

// این متد باعث می‌شود وقتی فیلتر تغییر کرد، صفحه‌بندی به صفحه ۱ برگردد
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterPriority() { $this->resetPage(); }
   public function resetFilters()
    {
    $this->reset(['filter_status', 'filter_priority', 'search']); // اگر می‌خواهید جستجوی متنی هم پاک شود
    $this->resetPage(); // حتماً به صفحه اول برگردد
    }
    protected function currentUserId()
    {
    return \App\Models\User::first()?->id;
    }
    public function updatedTaskView()
    {
    $this->resetPage();
    $this->open_activity_task_id = null;
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'priority' => 'required|in:low,normal,urgent',
             'due_date' => 'nullable|date',
        ];
    }

    /* ---------- CRUD ---------- */

public function cancelEdit()
{
    $this->reset(['taskId', 'title', 'description', 'unit_id', 'priority', 'due_date', 'assign_user_id', 'assign_user_search']);
    $this->resetErrorBag();
    $this->resetValidation();
    $this->dispatch('reset-datepicker');
}
public function edit($id)
{
    $this->resetErrorBag();
    $this->resetValidation();

    $task = Task::findOrFail($id);

    // امنیت: فقط ایجاد کننده
    if ($task->created_by !== 1) { // فعلاً دستی ۱
        session()->flash('error', 'عدم دسترسی برای ویرایش');
        return;
    }

    $this->taskId = $task->id;
    $this->title = $task->title;
    $this->description = $task->description;
    $this->unit_id = $task->unit_id;
    $this->priority = $task->priority;

    // تبدیل تاریخ میلادی دیتابیس به شمسی برای نمایش در Datepicker
    if ($task->due_date) {
        $shamsiDate = Jalalian::fromCarbon(Carbon::parse($task->due_date))->format('Y/m/d');
        $this->due_date = $shamsiDate;
        // ارسال رویداد به مرورگر برای پر کردن اینپوت JS
        $this->dispatch('set-datepicker', value: $shamsiDate);
    } else {
        $this->due_date = null;
        $this->dispatch('reset-datepicker');
    }
}

public function save()
{
    $this->validate([
        'title' => 'required|min:3',
        'unit_id' => 'required',
        'priority' => 'required',
        'due_date' => 'nullable', // فرمت شمسی توسط پکیج هندل می‌شود
    ]);

    // تبدیل تاریخ شمسی ورودی به میلادی برای ذخیره در دیتابیس
    $miladiDate = null;
    if (!empty($this->due_date)) {
        try {
            $miladiDate = Jalalian::fromFormat('Y/m/d', $this->due_date)->toCarbon()->toDateString();
        } catch (\Exception $e) {
            // اگر فرمت تاریخ اشتباه بود (پیشگیری از خطا)
            $miladiDate = null;
        }
    }

    if ($this->taskId) {
        // --- حالت ویرایش ---
        $task = Task::find($this->taskId);
        $task->update([
            'title' => $this->title,
            'description' => $this->description,
            'unit_id' => $this->unit_id,
            'priority' => $this->priority,
            'due_date' => $miladiDate, // ذخیره به صورت میلادی
            'task_status_id' => 1, // طبق رویکرد شما: بازگشت به وضعیت جدید
        ]);
        session()->flash('success', 'تسک با موفقیت ویرایش و وضعیت آن بازنشانی شد.');
    } else {
        // --- حالت ایجاد جدید ---
        $task = Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'unit_id' => $this->unit_id,
            'created_by' => 1,
            'task_status_id' => 1,
            'priority' => $this->priority,
            'due_date' => $miladiDate, // ذخیره به صورت میلادی
        ]);

        // ارجاع مستقیم در هنگام ثبت
        if ($this->assign_user_id) {
            $task->assignments()->create([
                'from_user_id' => 1,
                'to_user_id' => $this->assign_user_id,
                'description' => 'ارجاع مستقیم هنگام ثبت تسک',
            ]);
            $task->update(['task_status_id' => 2]); // وضعیت ارجاع شده
        }
        session()->flash('success', 'تسک جدید با موفقیت ثبت شد.');
    }

    // ریست کردن فرم و دیت‌پیکر
    $this->cancelEdit();
    $this->dispatch('reset-datepicker');
}
    public function delete($id)
    {
        Task::findOrFail($id)->delete();
        session()->flash('success', 'تسک حذف شد');
    }

    public function resetForm()
    {
        $this->reset([
            'taskId',
            'title',
            'description',
            'unit_id',
            'task_status_id',
            'priority',
             'due_date'
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    /*-------------change status------------*/
public function changeStatus($taskId, $newStatusId)
{
    $task = Task::findOrFail($taskId);

    if ($task->task_status_id == $newStatusId) {
        return;
    }

    $oldStatus = $task->task_status_id;

    $task->update([
        'task_status_id' => $newStatusId,
    ]);

    TaskActivity::create([
        'task_id' => $task->id,
        'user_id' => null, // بعداً Auth
        'action' => 'status_change',
        'old_status_id' => $oldStatus,
        'new_status_id' => $newStatusId,
        'description' => 'تغییر وضعیت تسک',
    ]);
}
    /*------------asign task -------*/
  public function assignTask()
{
    $this->validate([
        'assign_task_id' => 'required|exists:tasks,id',
        'assign_user_id' => 'required|exists:users,id',
    ]);

    $task = Task::findOrFail($this->assign_task_id);

    TaskAssignment::create([
        'task_id' => $task->id,
        'from_user_id' => null, // بعداً Auth
        'to_user_id' => $this->assign_user_id,
        'note' => 'ارجاع تسک',
    ]);

    $oldStatus = $task->task_status_id;
    $assignedStatus = TaskStatus::where('title', 'ارجاع شده')->first();

    $task->update([
        'task_status_id' => $assignedStatus->id,
    ]);

    TaskActivity::create([
        'task_id' => $task->id,
        'user_id' => null,
        'action' => 'assign',
        'old_status_id' => $oldStatus,
        'new_status_id' => $assignedStatus->id,
        'description' => 'ارجاع تسک',
    ]);

    $this->reset(['assign_task_id', 'assign_user_id']);
    session()->flash('success', 'تسک ارجاع شد');
}

    /* ---------- Render ---------- */

  public function render()
{
    $query = Task::with([
            'unit',
            'status',
            'activities.user',
            'activities.oldStatus',
            'activities.newStatus',
            'assignments.fromUser',
            'assignments.toUser',
        ])
        ->where(function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
              ->orWhereHas('unit', fn ($u) =>
                  $u->where('name', 'like', '%' . $this->search . '%')
              );
        });

    // --- شروع بخش فیلترهای جدید ---
    
    // فیلتر بر اساس وضعیت (اگر انتخاب شده باشد)
   if (filled($this->filter_status)) {
    $query->where('task_status_id', $this->filter_status);
}

    // فیلتر بر اساس اولویت (اگر انتخاب شده باشد)
    if (filled($this->filter_priority)) {
    $query->where('priority', $this->filter_priority);
}
    
    // --- پایان بخش فیلترهای جدید ---

    $currentUserId = $this->currentUserId();

    if ($this->task_view === 'inbox') {
        $query->whereHas('assignments', fn ($a) =>
            $a->where('to_user_id', $currentUserId)
        );
    }

    if ($this->task_view === 'sent') {
        $query->whereHas('assignments', fn ($a) =>
            $a->where('from_user_id', $currentUserId)
        );
    }

    $tasks = $query
        ->latest()
        ->paginate(10);

    $assignableUsers = \App\Models\User::where('is_active', true)
        ->where('full_name', 'like', '%' . $this->assign_user_search . '%')
        ->orderBy('full_name')
        ->limit(10)
        ->get();

    return view('livewire.tasks.task-index', [
        'tasks' => $tasks,
        'units' => Unit::orderBy('name')->get(),
        'statuses' => \App\Models\TaskStatus::all(), // این را برای نمایش در دراپ‌داون فیلتر اضافه کردیم
        'assignableUsers' => $assignableUsers,
    ]);
}

}
