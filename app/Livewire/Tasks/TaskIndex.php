<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Unit;
use App\Models\TaskStatus;
use App\Models\Attachment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\TaskActivity;
use App\Models\TaskAssignment;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
#[Layout('components.layouts.app')]
class TaskIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

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
    public $show_trash = false; // وضعیت نمایش زباله‌دان
    public $opened_attachments_id = null; // آیدی تسکی که پیوست‌هایش باز است
    public $files = [];
    public $commentContent = '';
    // متد ثبت کامنت
public function addComment($taskId)
{
    $this->validate([
        'commentContent' => 'required|min:2',
    ]);

    $task = Task::find($taskId);
    
    $task->comments()->create([
        'user_id' => null, // فعلاً نال
        'content' => $this->commentContent,
    ]);

    // ثبت در تاریخچه فعالیت‌ها
    $task->activities()->create([
        'user_id' => null,
        'action' => 'ثبت کامنت',
        'description' => 'یک کامنت جدید ثبت شد.',
    ]);

    $this->commentContent = ''; // خالی کردن فیلد بعد از ثبت
    session()->flash('success', 'نظر شما با موفقیت ثبت شد.');
}
public function removeFile($index)
{
    array_splice($this->files, $index, 1);
}
public function toggleAttachments($taskId)
{
    if ($this->opened_attachments_id === $taskId) {
        $this->opened_attachments_id = null;
    } else {
        $this->opened_attachments_id = $taskId;
        $this->open_activity_task_id = null; // بستن تاریخچه اگر باز بود
    }
}
public function updatedFiles()
{
    try {
        $this->validate([
            'files.*' => 'nullable|file|max:10240', // حداکثر ۱۰ مگابایت برای هر فایل
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // اگر فایلی بزرگتر بود، آرایه فایل‌ها را خالی می‌کنیم تا از ارسال فایل خراب جلوگیری شود
        $this->reset(['files']);
        throw $e;
    }
}
public function uploadMoreFiles($taskId)
{
   if (empty($this->files)) {
        session()->flash('error', 'لطفاً ابتدا فایلی انتخاب کنید.');
        return;
    }

    $this->validate([
        'files.*' => 'required|max:5120',
    ]);
    $task = Task::with('attachments')->findOrFail($taskId);
    
    // چک کردن محدودیت ۱۰ مگابایت مجموع
    $currentSize = $task->attachments->sum('file_size');
    $newSize = collect($this->files)->sum(fn($f) => $f->getSize());

    if (($currentSize + $newSize) > (10 * 1024 * 1024)) {
        session()->flash('error', 'مجموع فایل‌های این تسک از ۱۰ مگابایت فراتر می‌رود.');
        return;
    }

    foreach ($this->files as $file) {
        $path = $file->store('attachments', 'public');
        $task->attachments()->create([
            'user_id' => 1, // در آینده auth()->id()
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);
        // ثبت فعالیت
        $task->activities()->create([
            'user_id' => null,
            'action' => 'آپلود فایل',
            'description' => 'پیوست جدید اضافه شد: ' . $file->getClientOriginalName(),
        ]);
    }

    $this->reset(['files']);
    session()->flash('success', 'فایل‌ها با موفقیت اضافه شدند.');
}
// متد برای جابجایی بین لیست اصلی و زباله‌دان
public function toggleTrash()
{
    $this->show_trash = !$this->show_trash;
    $this->resetPage(); // برگشت به صفحه اول در هر جابجایی
}

// متد بازیابی تسک
public function restoreTask($id)
{
    $task = Task::withTrashed()->findOrFail($id);
    $task->restore();
    session()->flash('success', 'تسک با موفقیت بازیابی شد.');
}

// متد حذف دائمی (پاک کردن از دیتابیس)
public function forceDeleteTask($id)
{
    $task = Task::withTrashed()->with('attachments')->findOrFail($id);

    // پاک کردن فایل‌ها از حافظه هاست
    foreach ($task->attachments as $attachment) {
        Storage::disk('public')->delete($attachment->file_path);
    }

    $task->forceDelete(); // حذف رکورد تسک و ضمائم (بشرط داشتن cascade)
    session()->flash('success', 'تسک و تمام فایل‌های آن برای همیشه حذف شدند.');
}

// متد حذف تکی فایل در هنگام ویرایش
public function deleteAttachment($attachmentId)
{
    $attachment = Attachment::findOrFail($attachmentId);
    $fileName = $attachment->file_name;
    $taskId = $attachment->task_id;

    // حذف فیزیکی از هاست
    \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
    Storage::disk('public')->delete($attachment->file_path);
    $attachment->delete();
    // ثبت در لاگ فعالیت‌ها
    \App\Models\TaskActivity::create([
        'task_id' => $taskId,
        'user_id' => null,
        'action' => 'حذف فایل',
        'description' => 'فایل پیوست حذف شد: ' . $fileName,
    ]);
    
    session()->flash('success', 'فایل با موفقیت حذف شد.');
}
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
    try{

    
    // ۱. ولیدیشن فیلدهای متنی و پایه
    $rules = [
        'title' => 'required|min:3',
        'unit_id' => 'required',
        'priority' => 'required',
    ];

    // ولیدیشن فایل‌ها فقط اگر فایلی انتخاب شده باشد (برای جلوگیری از خطای No property found)
    if (!empty($this->files)) {
        $rules['files.*'] = 'nullable|max:5120'; // حداکثر ۵ مگابایت برای هر فایل
    }

    $this->validate($rules);

    // ۲. بررسی محدودیت ۱۰ مگابایت مجموع فایل‌ها برای این تسک
    $currentFilesSize = 0;
    if ($this->taskId) {
        $currentFilesSize = Attachment::where('task_id', $this->taskId)->sum('file_size');
    }
    
    // محاسبه حجم فایل‌های جدید در صف آپلود
    $newFilesSize = collect($this->files)->sum(fn($file) => $file->getSize());
    
    if (($currentFilesSize + $newFilesSize) > (10 * 1024 * 1024)) {
        $this->addError('files', 'مجموع حجم فایل‌های این تسک (قبلی + جدید) نمی‌تواند بیش از ۱۰ مگابایت باشد.');
        return;
    }

    // ۳. تبدیل تاریخ شمسی به میلادی برای دیتابیس
    $miladiDate = null;
    if (!empty($this->due_date)) {
        try {
            $miladiDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $this->due_date)->toCarbon()->toDateString();
        } catch (\Exception $e) {
            $miladiDate = null;
        }
    }

    // ۴. آماده‌سازی داده‌ها برای ایجاد یا بروزرسانی
    $taskData = [
        'title' => $this->title,
        'description' => $this->description,
        'unit_id' => $this->unit_id,
        'priority' => $this->priority,
        'due_date' => $miladiDate,
    ];

    if ($this->taskId) {
        // --- حالت ویرایش ---
        $task = Task::find($this->taskId);
        $task->update(array_merge($taskData, ['task_status_id' => 1]));
        session()->flash('success', 'تسک با موفقیت ویرایش و وضعیت آن بازنشانی شد.');
    } else {
        // --- حالت ثبت جدید ---
        $task = Task::create(array_merge($taskData, [
            'created_by' => 1, // در آینده auth()->id()
            'task_status_id' => 1
        ]));
        
        // ارجاع مستقیم در صورت انتخاب کاربر هنگام ثبت
        if ($this->assign_user_id) {
            $task->assignments()->create([
                'from_user_id' => 1,
                'to_user_id' => $this->assign_user_id,
                'description' => 'ارجاع مستقیم هنگام ثبت تسک',
            ]);
            $task->update(['task_status_id' => 2]); // تغییر وضعیت به "ارجاع شده"
        }
        session()->flash('success', 'تسک جدید با موفقیت ثبت شد.');
    }

    // ۵. پردازش و ذخیره فایل‌های پیوست (اگر فایلی انتخاب شده باشد)
    if (!empty($this->files)) {
        foreach ($this->files as $file) {
            // ذخیره فیزیکی در storage/app/public/attachments
            $path = $file->store('attachments', 'public');
            
            // ثبت در دیتابیس
            $task->attachments()->create([
                'user_id' => 1, // آیدی آپلود کننده
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
            // ثبت در لاگ فعالیت‌ها
        $task->activities()->create([
            'user_id' => null, 
            'action' => 'آپلود فایل',
            'description' => 'فایل جدید پیوست شد: ' . $file->getClientOriginalName(),
        ]);
        }
    }

    // ۶. پاکسازی فرم و ریست کردن دیت‌پیکر
    $this->cancelEdit();
    $this->dispatch('reset-datepicker');
    $this->reset(['files']); // حتماً آرایه فایل‌ها را برای تسک بعدی خالی کنید
} catch (\Illuminate\Validation\ValidationException $e) {
        // این بخش بسیار مهم است:
        // اجازه بده خودِ لایووایر خطاهای اعتبارسنجی را مدیریت کند
        throw $e; 

    } catch (\Exception $e) {
        // فقط خطاهای غیرمنتظره (مثل قطعی دیتابیس یا مشکل در ذخیره فایل) اینجا مدیریت شوند
        $this->addError('files', 'خطای فنی: ' . $e->getMessage());
        Log::error($e->getMessage());
    }
}

    public function delete($id)
    {
        Task::findOrFail($id)->delete();
session()->flash('success', 'تسک با موفقیت به زباله‌دان منتقل شد.');
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
    // ۱. ایجاد کوئری پایه با تمام روابط لازم
    $query = Task::with([
        'unit',
        'status',
        'activities.user',
        'activities.oldStatus',
        'activities.newStatus',
        'assignments.fromUser',
        'assignments.toUser',
        'attachments.user', // اضافه کردن این برای لود سریع‌تر فایل‌ها
        'comments.user',    
        
    ]);

    // ۲. بررسی حالت نمایش زباله‌دان
    if ($this->show_trash) {
        // فقط نمایش موارد حذف شده
        $query->onlyTrashed();
    } else {
        // --- اعمال فیلترهای لیست اصلی ---

        // جستجوی متنی (عنوان و واحد)
        $query->where(function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
              ->orWhereHas('unit', fn ($u) =>
                  $u->where('name', 'like', '%' . $this->search . '%')
              );
        });

        // فیلتر وضعیت
        if (filled($this->filter_status)) {
            $query->where('task_status_id', $this->filter_status);
        }

        // فیلتر اولویت
        if (filled($this->filter_priority)) {
            $query->where('priority', $this->filter_priority);
        }

        // فیلتر Inbox / Sent
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
    }

    // ۳. اجرای نهایی کوئری و صفحه‌بندی
    $tasks = $query->latest()->paginate(10);

    // ۴. دریافت لیست کاربران برای بخش ارجاع (جستجوی لایو)
    $assignableUsers = \App\Models\User::where('is_active', true)
        ->where('full_name', 'like', '%' . $this->assign_user_search . '%')
        ->orderBy('full_name')
        ->limit(10)
        ->get();
$latestAttachments = Attachment::whereIn('task_id', $tasks->pluck('id'))
    ->with('user') // برای اینکه نام آپلود کننده را داشته باشیم
    ->latest()
    ->get();
    return view('livewire.tasks.task-index', [
        'tasks' => $tasks,
        'units' => Unit::orderBy('name')->get(),
        'statuses' => \App\Models\TaskStatus::all(),
        'assignableUsers' => $assignableUsers,
        'latestAttachments' => $latestAttachments,
    ]);
}

}
