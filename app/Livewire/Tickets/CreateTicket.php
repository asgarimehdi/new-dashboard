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
    $this->unit_id = null; // اگر کاربر دوباره تایپ کرد، انتخاب قبلی باطل شود
    $this->showDropdown = true;
}
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
    $this->validate([
        'unit_id' => 'required',
        'subject' => 'required|min:5',
        'content' => 'required|min:10',
        'files.*' => 'nullable|max:10240',
    ]);

    // ۱. ایجاد تیکت
    $ticket = Ticket::create([
        'ticket_code' => 'TK-' . strtoupper(uniqid()),
        'user_id' => auth()->id() ?? 1,
        'unit_id' => $this->unit_id,
        'subject' => $this->subject,
        'content' => $this->content,
        'priority' => $this->priority,
        'status' => 'open', // وضعیت اولیه
        'is_task' => false,
    ]);

    // ۲. ثبت اولین فعالیت (تایم‌لاین) در جدول task_activities
    $ticket->activities()->create([
        'user_id' => auth()->id() ?? 1,
        'action' => 'created',
        'description' => 'تیکت توسط کاربر ایجاد و به واحد مقصد ارسال شد.',
        'new_status' => 'open',
    ]);

    // ۳. ذخیره فایل‌ها (در صورت وجود)
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

    session()->flash('message', 'تیکت با موفقیت ثبت شد.');
    return redirect()->route('tickets.create'); // یا هر مسیری که داری
}
    public function render()
    {
       $units = [];
    if (strlen($this->search) >= 2) {
        $units = Unit::where('can_receive_tickets', true)
            ->where('is_active', true)
            ->where('name', 'like', '%' . $this->search . '%')
            ->take(10) // محدود کردن برای سرعت بیشتر
            ->get();
    }

    return view('livewire.tickets.create-ticket', compact('units'));
    }
}