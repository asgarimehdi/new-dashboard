<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Unit;
use App\Models\TaskStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

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

        $statusNew = TaskStatus::where('title', 'جدید')->first();

        Task::updateOrCreate(
            ['id' => $this->taskId],
            [
                'title' => $this->title,
                'description' => $this->description,
                'unit_id' => $this->unit_id,
                'task_status_id' => $statusNew->id,
                'created_by' => null, // بعداً Auth
            ]
        );

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

    /* ---------- Render ---------- */

    public function render()
    {
        $tasks = Task::with(['unit', 'status'])
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
