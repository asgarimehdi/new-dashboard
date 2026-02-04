<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class TicketInbox extends Component
{
    use WithPagination;

    public $search = '';
    
    // رندر کردن تیکت‌های ورودی که هنوز تایید نشده‌اند
   public function render()
{
    // دریافت واحد کاربر فعلی
    $userUnitId = auth()->user()->unit_id ?? null;

    $query = Ticket::query()
        ->where('is_task', false)
        ->where('status', 'open');

    // اگر کاربر عضو واحدی بود، فقط تیکت‌های همان واحد را نشان بده
    if ($userUnitId) {
        $query->where('unit_id', $userUnitId);
    } else {
        // اگر واحد نداشت (مثلاً مدیر کل بود)، موقتاً همه تیکت‌های باز را نشان بده
        // یا اگر می‌خواهید هیچ‌چیز نبیند: $query->whereRaw('1 = 0');
    }

    if ($this->search) {
        $query->where(function($q) {
            $q->where('subject', 'like', '%' . $this->search . '%')
              ->orWhere('ticket_code', 'like', '%' . $this->search . '%');
        });
    }

    return view('livewire.tickets.ticket-inbox', [
        'tickets' => $query->with('creator')->latest()->paginate(15)
    ]);
}
public $showingTicket = null; // برای ذخیره تیکت انتخابی جهت نمایش در مودال

public function showTicket($id)
{
    // بارگذاری تیکت به همراه فعالیت‌ها و فایل‌ها
    $this->showingTicket = Ticket::with(['activities.user', 'attachments', 'creator'])->findOrFail($id);
}

public function closeDetail()
{
    $this->showingTicket = null;
}
    public $selectedTicketId;
public $newUnitId;

// متد برای تغییر واحد تیکت
public $selectedUnit = []; // برای ذخیره واحد انتخاب شده برای هر تیکت

public function forwardTicket($ticketId)
{
    // چک کردن اینکه آیا واحدی برای این تیکت خاص انتخاب شده یا نه
    if (!isset($this->selectedUnit[$ticketId]) || empty($this->selectedUnit[$ticketId])) {
        $this->dispatch('swal', title: 'لطفاً ابتدا یک واحد را انتخاب کنید', icon: 'error');
        return;
    }

    $newUnitId = $this->selectedUnit[$ticketId];
    $ticket = Ticket::findOrFail($ticketId);
    
    $oldUnitName = $ticket->unit->name ?? 'نامشخص';
    $newUnit = Unit::find($newUnitId);

    DB::transaction(function () use ($ticket, $newUnitId, $oldUnitName, $newUnit) {
        $ticket->update(['unit_id' => $newUnitId]);
        
        $ticket->logActivity(
            "تیکت از واحد $oldUnitName به واحد {$newUnit->name} ارجاع داده شد.", 
            'forwarded'
        );
    });

    $this->dispatch('swal', title: 'با موفقیت به واحد ' . $newUnit->name . ' ارجاع شد', icon: 'success');
    
    // پاک کردن انتخاب بعد از انجام عملیات
    unset($this->selectedUnit[$ticketId]);
}
public function setForward($unitId, $ticketId)
{
    if(!$unitId) return;
    $this->newUnitId = $unitId;
    $this->selectedTicketId = $ticketId;
    $this->forwardTicket();
}
    // متد تایید و تبدیل به تسک (که قبلاً با هم اصلاح کردیم)
    public function acceptTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $currentUserId = auth()->id() ?? 1;

        DB::transaction(function () use ($ticket, $currentUserId) {
            $ticket->update([
                'is_task' => true,
                'status' => 'processing',
                'task_status_id' => 1, // وضعیت شروع در جدول task_statuses
                'accepted_at' => now(),
            ]);

            $ticket->logActivity('تیکت تایید شد و به فاز اجرایی وارد شد.', 'converted_to_task');

            $ticket->assignments()->create([
                'from_user_id' => $currentUserId,
                'to_user_id' => $currentUserId,
                'status' => 'pending'
            ]);
        });

        session()->flash('success', 'تیکت تایید شد و به لیست تسک‌ها منتقل شد.');
    }

    // متد رد تیکت
    public function rejectTicket($id, $reason = 'شرایط لازم را ندارد')
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => 'rejected']);
        $ticket->logActivity("تیکت رد شد. دلیل: $reason", 'rejected');
        
        session()->flash('info', 'تیکت رد شد.');
    }
}