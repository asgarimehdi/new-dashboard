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

    // این متد بلافاصله بعد از انتخاب فایل توسط کاربر اجرا می‌شود
    public function updatedFiles()
    {
        $this->resetErrorBag('files');

        // ۱. کنترل تعداد (سخت‌گیرانه)
        if (count($this->files) > 5) {
            $this->addError('files', 'خطا: حداکثر مجاز به انتخاب ۵ فایل هستید.');
            $this->files = []; // پاکسازی برای امنیت
            return;
        }

        // ۲. کنترل نوع و حجم انفرادی هر فایل
        try {
            $this->validate([
                'files.*' => 'mimes:jpg,jpeg,png,pdf,zip,rar|max:5120', // حداکثر ۵ مگابایت برای هر فایل
            ], [
                'files.*.mimes' => 'خطا: فرمت فایل انتخابی غیرمجاز است.',
                'files.*.max' => 'خطا: حجم هر فایل نباید بیش از ۵ مگابایت باشد.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->files = []; // در صورت وجود فایل غیرمجاز، لیست را خالی کن
            throw $e;
        }

        // ۳. کنترل مجموع حجم کل فایل‌ها
        $totalSizeInBytes = 0;
        foreach ($this->files as $file) {
            $totalSizeInBytes += $file->getSize();
        }

        if ($totalSizeInBytes > 10 * 1024 * 1024) { // ۱۰ مگابایت به بایت
            $this->addError('files', 'خطا: مجموع حجم فایل‌ها نباید بیش از ۱۰ مگابایت باشد.');
            $this->files = [];
            return;
        }
    }

    public function removeFile($index)
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files); // ریست کردن ایندکس‌ها
        }
    }

    public function saveTicket()
    {
        // اعتبارسنجی نهایی تمام فیلدها
        $validatedData = $this->validate([
            'unit_id' => 'required|exists:units,id',
            'subject' => 'required|min:5|max:255',
            'content' => 'required|min:10',
            'priority' => 'required|in:low,normal,urgent',
            'files' => 'nullable|array|max:5',
        ]);

        $ticketCode = 'TIC-' . now()->format('Ymd') . '-' . rand(1000, 9999);

        // استفاده از Transaction برای اطمینان از صحت ثبت تیکت و فایل‌ها با هم
        \DB::transaction(function () use ($ticketCode) {
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
                    $path = $file->store('attachments/tickets', 'public');
                    $ticket->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'user_id'   => auth()->id() ?? 1,
                    ]);
                }
            }
            
            session()->flash('ticket_code', $ticketCode);
        });

        session()->flash('success', "درخواست شما با موفقیت در سیستم ثبت شد.");
        return redirect()->to(route('tickets.create'));
    }

    public function render()
    {
        return view('livewire.tickets.create-ticket', [
            'units' => Unit::where('is_active', true)
            ->where('can_receive_tickets', true)
             ->orderBy('name')
             ->get()
        ]);
    }
}