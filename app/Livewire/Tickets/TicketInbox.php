<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class TicketInbox extends Component
{
    use WithPagination;

    public $search = ''; 
    public $unitSearch = ''; 
    public $targetUnitId = null;
    public $targetUnitName = '';
    public $forwardNote = '';
    public $showingTicket = null;

    public function render()
{
    $units = [];
    if (strlen($this->unitSearch) > 1) {
        $units = Unit::where('name', 'like', '%' . $this->unitSearch . '%')
                    ->where('can_receive_tickets', true)
                    ->limit(5)->get();
    }

   $query = Ticket::with(['user', 'assignee']) // اصلاح شد: creator به user تغییر یافت
                   ->where('unit_id', auth()->user()->unit_id)
                   ->where('status', '!=', 'rejected');

    if (!empty($this->search)) {
        $query->where(function($q) {
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

        $this->dispatch('swal',[ 'title'=> 'تیکت با موفقیت ارجاع شد', 'icon'=> 'success']);
        $this->closeDetail();

    } catch (\Exception $e) {
        $this->dispatch('swal',[ 'title'=> 'خطا در انجام عملیات', 'icon'=> 'error']);
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

        $this->dispatch('swal', ['title'=> 'تیکت با موفقیت رد شد', 'icon'=> 'info']);
        $this->closeDetail();

    } catch (\Exception $e) {
        $this->dispatch('swal', ['title'=> 'خطایی رخ داد', 'icon'=> 'error']);
        $this->closeDetail();
    }
}
}