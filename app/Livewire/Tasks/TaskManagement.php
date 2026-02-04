<?php

namespace App\Livewire\Tasks;

use App\Models\Ticket;
use App\Models\TaskStatus;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class TaskManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $viewMode = 'inbox'; 
    public $subject, $content, $priority = 'normal', $unit_id;

  public function render()
{
    $query = Ticket::where('is_task', true);

    if ($this->search) {
        $query->where('subject', 'like', '%' . $this->search . '%');
    }

    // اصلاح رابطه‌ها در اینجا
    return view('livewire.tasks.task-management', [
        'tasks' => $query->with(['taskStatus', 'creator'])->latest()->paginate(10),
        'units' => Unit::all(),
        'statuses' => TaskStatus::all()
    ]);
}
    public function createDirectTask()
    {
        $this->validate([
            'subject' => 'required|min:3',
            'content' => 'required',
            'unit_id' => 'required'
        ]);

        DB::transaction(function () {
            $task = Ticket::create([
                'subject' => $this->subject,
                'content' => $this->content,
                'priority' => $this->priority,
                'unit_id' => $this->unit_id,
                'is_task' => true,
                'status' => 'processing',
                'task_status_id' => 1,
                'created_by' => auth()->id() ?? 1,
                'ticket_code' => 'TASK-' . rand(1000, 9999) // تولید کد موقت
            ]);

            // ثبت اولین فعالیت در جدول جدید
            $task->logActivity('تسک مستقیماً ایجاد شد.', 'system');
        });

        $this->reset(['subject', 'content', 'unit_id']);
        session()->flash('success', 'تسک با موفقیت ثبت شد.');
    }
}