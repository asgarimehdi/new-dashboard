<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class InboxTickets extends Component
{
    use WithPagination;
    public $selectedTicketId;
    public $assign_user_search = '';
    public $showAssignModal = false;
    public $selectedTicket = null;

    // متد تبدیل تیکت به تسک و ارجاع
   public function openAssignModal($id)
{
    $this->selectedTicketId = $id;
    $this->dispatch('open-modal-assign'); // ارسال سیگنال باز شدن مدال
}

// ۱. متد مخصوص قبول تیکت (ارجاع به خود)
public function acceptTicket($ticketId)
{
    $this->selectedTicketId = $ticketId;
    $this->convertAndAssign(1); // ارجاع به مدیر (آیدی 1)
}

// ۲. متد مخصوص ارجاع به دیگران (توسط مدال صدا زده می‌شود)
public function assignToUser($userId)
{
    $this->convertAndAssign($userId);
}

// ۳. متد هسته مرکزی برای انجام تراکنش (Private)
private function convertAndAssign($toUserId)
{
    $ticket = Ticket::with('attachments')->findOrFail($this->selectedTicketId);

    DB::transaction(function () use ($ticket, $toUserId) {
        // ایجاد تسک
        $task = Task::create([
            'ticket_id'      => $ticket->id,
            'title'          => 'ارجاعی: ' . $ticket->subject,
            'description'    => $ticket->content,
            'unit_id'        => $ticket->unit_id,
            'created_by'     => 1, // موقت
            'task_status_id' => 1,
            'priority'       => $ticket->priority,
        ]);

        // انتقال فایل‌ها
        foreach ($ticket->attachments as $att) {
            $task->attachments()->create([
                'file_path' => $att->file_path,
                'file_name' => $att->file_name,
                'file_size' => $att->file_size,
                'user_id'   => 1,
            ]);
        }

        // ایجاد ارجاع
        $task->assignments()->create([
            'from_user_id' => 1,
            'to_user_id'   => $toUserId,
            'status'       => 'pending'
        ]);

        // بستن تیکت
        $ticket->update(['status' => 'processing']);
    });

    $this->reset(['selectedTicketId', 'assign_user_search']);
    $this->dispatch('close-modal'); // بستن مدال پس از موفقیت
    session()->flash('success', 'عملیات با موفقیت انجام شد.');
}
    // افزودن متد رد کردن تیکت
public function rejectTicket($ticketId)
{
    Ticket::findOrFail($ticketId)->update(['status' => 'rejected']);
    session()->flash('success', 'تیکت رد شد.');
}

    public function render()
    {
        $tickets = Ticket::with('attachments') // لود کردن پیوست‌ها
        ->where('status', 'open')->latest()->paginate(10);

       // دریافت آیدی ۵ کاربر اخیری که به آن‌ها ارجاع داده‌اید
$recentUserIds = \App\Models\TaskAssignment::where('from_user_id', 1) // آیدی خودتان
    ->latest()
    ->distinct()
    ->pluck('to_user_id')
    ->take(5);

$assignableUsers = \App\Models\User::where('is_active', true)
    ->when(empty($this->assign_user_search), function ($query) use ($recentUserIds) {
        // اگر جستجو خالی است، ۵ نفر اخیر را در اولویت قرار بده
        return $query->whereIn('id', $recentUserIds);
    })
    ->when(!empty($this->assign_user_search), function ($query) {
        // اگر در حال جستجو است، طبق جستجو فیلتر کن
        return $query->where('full_name', 'like', '%' . $this->assign_user_search . '%');
    })
    ->limit(10)
    ->get();

        return view('livewire.tickets.inbox-tickets', [
            'tickets' => $tickets,
            'assignableUsers' => $assignableUsers
        ]);
    }
}