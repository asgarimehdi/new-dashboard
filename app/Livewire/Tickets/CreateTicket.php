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

    public $search = '';
    public $unit_id = null;
    public $showDropdown = false;
    public $subject, $content, $priority = 'normal';
    public $files = [];

    public function selectUnit($id, $name)
    {
        $this->unit_id = $id;
        $this->search = $name;
        $this->showDropdown = false;
    }

    public function updatedSearch()
    {
        $this->unit_id = null;
        $this->showDropdown = true;
    }

    public function updatedFiles()
    {
        $this->resetErrorBag('files');
        if (count($this->files) > 5) {
            $this->addError('files', 'حداکثر ۵ فایل مجاز است.');
            $this->files = [];
            return;
        }
        $this->validate(['files.*' => 'mimes:jpg,jpeg,png,pdf,zip,rar|max:5120']);
    }

    public function removeFile($index)
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files);
        }
    }

    public function saveTicket()
    {
        $this->validate([
            'unit_id' => 'required',
            'subject' => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        $ticketCode = 'TK-' . strtoupper(substr(uniqid(), -6));

        $ticket = Ticket::create([
            'ticket_code' => $ticketCode,
            'user_id' => auth()->id() ?? 1,
            'unit_id' => $this->unit_id,
            'subject' => $this->subject,
            'content' => $this->content,
            'priority' => $this->priority,
            'status' => 'open',
            'is_task' => false,
        ]);

        $ticket->activities()->create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'created',
            'description' => 'تیکت ایجاد شد.',
            'new_status' => 'open',
        ]);

        if ($this->files) {
            foreach ($this->files as $file) {
                $path = $file->store('attachments', 'public');
                $ticket->attachments()->create([
                    'user_id' => auth()->id() ?? 1,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // ذخیره کد پیگیری در سشن برای نمایش پس از ریدایرکت
        session()->flash('success', 'تیکت با موفقیت ثبت شد.');
        session()->flash('ticket_code', $ticketCode);

        return redirect()->route('tickets.create');
    }

    public function render()
    {
        $units = [];
        if (strlen($this->search) >= 2) {
            $units = Unit::where('can_receive_tickets', true)
                ->where('is_active', true)
                ->where('name', 'like', '%' . $this->search . '%')
                ->take(5)
                ->get();
        }
        return view('livewire.tickets.create-ticket', compact('units'));
    }
}