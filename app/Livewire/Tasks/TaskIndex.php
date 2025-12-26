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
#[Layout('components.layouts.app')]
class TaskIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $taskId = null;

    public $title = '';
    public $description = '';
    public $unit_id = '';
    public $task_status_id = null;

    public $search = '';
    public $assign_task_id = null;
    public $assign_user_id = null;

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
        ];
    }

    /* ---------- CRUD ---------- */
public function save()
{
    $this->validate();

    if ($this->taskId) {
        // ویرایش Task → وضعیت دست نخورَد
        Task::findOrFail($this->taskId)->update([
            'title' => $this->title,
            'description' => $this->description,
            'unit_id' => $this->unit_id,
        ]);
    } else {
        // ایجاد Task جدید → وضعیت = جدید
        $statusNew = TaskStatus::where('title', 'جدید')->first();

        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'unit_id' => $this->unit_id,
            'task_status_id' => $statusNew->id,
            'created_by' => null, // بعداً Auth
        ]);
    }

    $this->resetForm();
    session()->flash('success', 'تسک ذخیره شد');
}



    public function edit($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $task = Task::findOrFail($id);

        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->unit_id = $task->unit_id;
        $this->task_status_id = $task->task_status_id;
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
        // $tasks = Task::with(['unit', 'status'])
        $tasks = Task::with(['unit', 'status', 'activities.oldStatus', 'activities.newStatus'])
            ->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('unit', fn ($u) =>
                      $u->where('name', 'like', '%' . $this->search . '%')
                  );
            })
            ->latest()
            ->paginate(10);

        return view('livewire.tasks.task-index', [
            'tasks' => $tasks,
            'units' => Unit::orderBy('name')->get(),
        ]);
    }
}
