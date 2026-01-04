<?php
namespace App\Livewire\Tickets;

use App\Models\Unit;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')] 
class CreateTicket extends Component
{
    use WithFileUploads;

    public $unit_id, $subject, $content, $priority = 'normal';
    public $files = [];

    protected $rules = [
        'unit_id' => 'required|exists:units,id',
        'subject' => 'required|min:5|max:255',
        'content' => 'required|min:10',
        'priority' => 'required|in:low,normal,urgent',
        'files.*' => 'nullable|max:10240',
    ];

    public function saveTicket()
    {
        $this->validate();

        $ticketCode = 'TIC-' . now()->format('Ymd') . '-' . rand(1000, 9999);

        $ticket = Ticket::create([
            'ticket_code' => $ticketCode,
            'user_id'     => auth()->id() ?? 1, 
            'unit_id'     => $this->unit_id,
            'subject'     => $this->subject,
            'content'     => $this->content,
            'priority'    => $this->priority,
            'status'      => 'open',
        ]);

      if ($this->files) {
    foreach ($this->files as $file) {
        // ذخیره فیزیکی فایل
        $path = $file->store('attachments/tickets', 'public');
        
        // ثبت در دیتابیس با رعایت تمام فیلدهای اجباری
        $ticket->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(), // اضافه کردن سایز فایل برای رفع خطای شما
            'user_id'   => auth()->id() ?? 1,
            // فیلد ticket_id به صورت خودکار توسط رابطه Eloquent پر می‌شود
        ]);
    }
}

       session()->flash('success', "درخواست شما ثبت شد.");
session()->flash('ticket_code', $ticketCode);
        return redirect()->to(route('tickets.create'));
    }

    public function render()
    {
        // نمایش واحدهای فعال برای انتخاب (مثلاً واحدهایی که زیرمجموعه هستند)
        return view('livewire.tickets.create-ticket', [
            'units' => Unit::where('is_active', true)->get()
        ]);
    }
}