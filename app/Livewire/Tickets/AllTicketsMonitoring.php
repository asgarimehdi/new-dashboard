<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Morilog\Jalali\Jalalian;

#[Layout('layouts.app')]
class AllTicketsMonitoring extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $selectedUnitId = null; // برای فیلتر کردن بر اساس واحد
    public $unitSearch = '';
    public $showingTicket = null;
    public $targetUnitId = null;
    public $targetUnitName = '';
    public $forwardNote = '';
    public $units = []; // برای سازگاری با کد مودال کپی شده
    public $dateFrom = '';
    public $dateTo = '';
    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }
    // متد برای انتخاب واحد جهت فیلتر لیست
    public function selectUnitForFilter($id)
    {
        $this->selectedUnitId = $id;
        $this->unitSearch = ''; // پاک کردن متن جستجو
        $this->resetPage();
    }

    public function render()
    {
        // جستجوی واحدها برای فیلتر بالای صفحه
        $units = [];
        if (strlen($this->unitSearch) > 1) {
            $units = Unit::where('name', 'like', '%' . $this->unitSearch . '%')
                ->limit(10)->get();
        }

        $query = Ticket::with(['user', 'unit', 'assignee']);

        // ۱. فیلتر بر اساس واحد انتخاب شده (اگر انتخاب شده باشد)
        if ($this->selectedUnitId) {
            $query->where('unit_id', $this->selectedUnitId);
        }

        // ۲. فیلتر وضعیت
        if ($this->statusFilter === 'pending') {
            $query->whereIn('status', ['created', 'forwarded']);
        } elseif ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // ۳. جستجو در متن
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('ticket_code', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->dateFrom) {
            $miladiFrom = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $this->dateFrom)->toCarbon()->startOfDay();
            $query->where('created_at', '>=', $miladiFrom);
        }

        if ($this->dateTo) {
            $miladiTo = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $this->dateTo)->toCarbon()->endOfDay();
            $query->where('created_at', '<=', $miladiTo);
        }
        return view('livewire.tickets.all-tickets-monitoring', [
            'tickets' => $query->latest()->paginate(15),
            'filterUnits' => $units,
            'currentUnit' => $this->selectedUnitId ? Unit::find($this->selectedUnitId) : null
        ]);
    }

    public function showTicket($id)
    {
        $this->showingTicket = Ticket::with(['activities.user', 'attachments', 'user', 'unit'])->findOrFail($id);
    }

    public function closeDetail()
    {
        $this->showingTicket = null;
    }
}
