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
    public $unitSearch = ''; 
    public $targetUnitId = null;
    public $targetUnitName = '';
    public $forwardNote = '';
    public $showingTicket = null; // برای کنترل کل مودال

    public function render()
{
    $units = [];
    if (strlen($this->unitSearch) > 1) {
        $units = Unit::where('name', 'like', '%' . $this->unitSearch . '%')
                    ->where('can_receive_tickets', true)
                    ->limit(5)->get();
    }

    // اصلاح کوئری برای اعمال جستجو
    $query = Ticket::where('is_task', false)
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
        $this->showingTicket = Ticket::with(['activities.user', 'attachments', 'creator', 'unit'])->findOrFail($id);
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
            'targetUnitId' => 'required',
            'forwardNote' => 'nullable|max:500',
        ]);

        DB::transaction(function () {
            $oldUnitName = $this->showingTicket->unit->name;
            $newUnit = Unit::find($this->targetUnitId);

            $this->showingTicket->update([
                'unit_id' => $newUnit->id,
                'status' => 'forwarded'
            ]);

            $this->showingTicket->activities()->create([
                'user_id' => 1, // Default user for now
                'action' => 'forwarded',
                'description' => "ارجاع از «{$oldUnitName}» به «{$newUnit->name}» - یادداشت: " . ($this->forwardNote ?: 'بدون یادداشت'),
                'to_unit_id' => $newUnit->id,
            ]);
        });

        $this->dispatch('swal', ['title' => 'Success', 'icon' => 'success']);
        $this->closeDetail();
    }

    public function acceptTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'is_task' => true,
                'status' => 'processing',
                'accepted_at' => now(),
            ]);

            $ticket->activities()->create([
                'user_id' => 1,
                'action' => 'accepted',
                'description' => 'تیکت تایید شد و به فاز اجرایی وارد شد.'
            ]);
        });
        $this->dispatch('swal', ['title' => 'تایید شد', 'icon' => 'success']);
        $this->closeDetail();
    }

    public function rejectTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        DB::transaction(function () use ($ticket) {
            $ticket->update(['status' => 'rejected']);
            $ticket->activities()->create([
                'user_id' => 1,
                'action' => 'rejected',
                'description' => 'تیکت توسط واحد مقصد رد شد.'
            ]);
        });
        $this->dispatch('swal', ['title' => 'تیکت رد شد', 'icon' => 'info']);
        $this->closeDetail();
    }
}