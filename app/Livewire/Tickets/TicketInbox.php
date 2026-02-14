<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;


#[Layout('layouts.app')]
class TicketInbox extends Component
{
    use WithPagination;
    // داخل کلاس حتما این تریت باشد
    use WithFileUploads;

    public $isCompletionModalOpen = false; // برای مدیریت مودال کوچک تایید نهایی
    public $completionNote = '';
    public $completionFiles = [];
    public $search = '';
    public $unitSearch = '';
    public $targetUnitId = null;
    public $targetUnitName = '';
    public $forwardNote = '';
    public $showingTicket = null;
    public $dateFrom = '';
    public $dateTo = '';
    public $currentTab = 'pending'; // تب پیش‌فرض: در انتظار بررسی
    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }
    // متد برای تغییر تب
    public function setTab($tab)
    {
        $this->currentTab = $tab;
        $this->resetPage(); // برگشت به صفحه اول در صورت استفاده از پجینیشن
    }

    public $viewMode = 'received';
    public $statusFilter = 'pending'; // مقدار پیش‌فرض

    public function render()
    {
        $user = auth()->user();
        $units = [];

        // همان منطق جستجوی واحدها که داشتی برای بخش ارجاع
        if (strlen($this->unitSearch) > 1) {
            $units = Unit::where('name', 'like', '%' . $this->unitSearch . '%')
                ->where('can_receive_tickets', true)
                ->limit(5)->get();
        }

        $query = Ticket::with(['user', 'unit', 'assignee']);

        // --- فیلتر بر اساس جهت تیکت (ورودی / خروجی) ---
        if ($this->viewMode === 'received') {
            // تیکت‌هایی که به واحد من آمده است
            $query->where('unit_id', $user->unit_id);
        } else {
            // تیکت‌هایی که من خودم ایجاد کرده‌ام
            $query->where('user_id', $user->id);
        }

        // --- فیلتر وضعیت‌ها بر اساس تب انتخاب شده ---
        if ($this->statusFilter === 'pending') {
            $query->whereIn('status', ['created', 'forwarded']);
        } elseif ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }
        if ($this->dateFrom) {
            $miladiFrom = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $this->dateFrom)->toCarbon()->startOfDay();
            $query->where('created_at', '>=', $miladiFrom);
        }

        if ($this->dateTo) {
            $miladiTo = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $this->dateTo)->toCarbon()->endOfDay();
            $query->where('created_at', '<=', $miladiTo);
        }
        // --- منطق جستجوی متن ---
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('ticket_code', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.tickets.ticket-inbox', [
            'tickets' => $query->latest()->paginate(15),
            'units' => $units
        ]);
    }

    public function showTicket($id)
    {
        // لود کردن رابطه‌ها بر اساس نام‌های درست در مدل‌ها
        $this->showingTicket = Ticket::with(['activities.user', 'attachments', 'user', 'unit'])->findOrFail($id);
    }

    public function closeDetail()
    {
        $this->showingTicket = null;
        $this->reset(['targetUnitId', 'targetUnitName', 'unitSearch', 'forwardNote']);
    }

    public function selectTargetUnit($id, $name)
    {
        $this->targetUnitId = $id;
        $this->targetUnitName = $name;
        $this->unitSearch = '';
    }

    public function forward()
    {
        $this->validate([
            'targetUnitId' => 'required|exists:units,id',
            'forwardNote' => 'nullable|string|max:500',
        ]);

        try {
            \DB::transaction(function () {
                // ثبت در دیتابیس
                $this->showingTicket->update([
                    'unit_id' => $this->targetUnitId,
                    'status' => 'forwarded',
                    'current_assignee_id' => null // چون به واحد جدید رفته، هنوز کسی مسئولش نیست
                ]);

                // ثبت فعالیت در تاریخچه
                $this->showingTicket->activities()->create([
                    'user_id' => auth()->id(),
                    'action' => 'forwarded',
                    'description' => "ارجاع تیکت به واحد: " . $this->targetUnitName . " - توضیحات: " . $this->forwardNote,
                ]);
            });

            $this->dispatch('swal', ['title' => 'تیکت با موفقیت ارجاع شد', 'icon' => 'success']);
            $this->closeDetail();
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'خطا در انجام عملیات', 'icon' => 'error']);
        }
    }

    public function acceptTicket($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'status' => 'accepted',
                'current_assignee_id' => auth()->id(), // آی‌دی کاربر لاگین شده
                'accepted_at' => now(),
            ]);

            $ticket->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'accepted',
                'description' => 'تیکت توسط کارشناس تایید شد و مسئولیت آن پذیرفته شد.'
            ]);
        });

        $this->dispatch('swal', ['title' => 'تیکت پذیرفته شد', 'icon' => 'success']);
        $this->closeDetail();
    }

    public function rejectTicket($ticketId)
    {
        try {
            $ticket = Ticket::where('unit_id', auth()->user()->unit_id)->findOrFail($ticketId);

            \DB::transaction(function () use ($ticket) {
                $ticket->update([
                    'status' => 'rejected',
                    'current_assignee_id' => auth()->id(), // کسی که تیکت را رد کرده
                ]);

                // ثبت در تاریخچه فعالیت‌ها
                $ticket->activities()->create([
                    'user_id' => auth()->id(),
                    'action' => 'rejected',
                    'description' => 'تیکت توسط واحد ' . (auth()->user()->unit->name ?? '') . ' رد شد.',
                ]);
            });

            $this->dispatch('swal', ['title' => 'تیکت با موفقیت رد شد', 'icon' => 'info']);
            $this->closeDetail();
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'خطایی رخ داد', 'icon' => 'error']);
            $this->closeDetail();
        }
    }
    // ۲. باز کردن مودال کوچک برای اتمام کار
public function openCompletionModal()
{
    $this->reset(['completionNote', 'completionFiles']);
    $this->isCompletionModalOpen = true;
}

// ۳. متد نهایی تکمیل تیکت
public function completeTicket()
{
    $ticket = $this->showingTicket;

    $this->validate([
        'completionNote' => 'required|min:5',
        'completionFiles.*' => 'mimes:jpg,jpeg,png,pdf,zip,rar|max:5120'
    ]);

    // تغییر وضعیت تیکت
    $ticket->update(['status' => 'completed']);

    // ثبت فعالیت اتمام
    $ticket->activities()->create([
        'user_id' => auth()->id(),
        'action' => 'completed',
        'description' => 'تیکت تکمیل شد. گزارش: ' . $this->completionNote,
        'to_unit_id' => $ticket->unit_id,
        'is_internal' => false,
    ]);

    // ثبت فایل‌های خروجی (با همان ساختار CreateTicket)
    if ($this->completionFiles) {
        foreach ($this->completionFiles as $file) {
            $path = $file->store('attachments', 'public');
            $ticket->attachments()->create([
                'user_id' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    $this->isCompletionModalOpen = false;
    $this->closeDetail(); // بستن مودال اصلی
    $this->dispatch('swal', ['title' => 'خسته نباشید! تیکت با موفقیت بسته شد.', 'icon' => 'success']);
}
}
