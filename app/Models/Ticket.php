<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_code', 'user_id', 'unit_id', 'subject', 
        'content', 'priority', 'status', 'task_id','is_task','task_status_id',
    'accepted_at',
    'completed_at',
    ];
// تعریف status_name برای نمایش فارسی وضعیت‌های تیکت
public function getStatusNameAttribute()
{
    return match($this->status) {
        'open' => 'در انتظار بررسی',
        'processing' => 'در حال انجام (تسک)',
        'closed' => 'بسته شده',
        'rejected' => 'رد شده',
        default => 'نامشخص',
    };
}
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
  
// رابطه با فعالیت‌ها
public function activities()
{
    // دقت کنید که در مایگریشن جدید نام فیلد را ticket_id گذاشتیم
    return $this->hasMany(TaskActivity::class, 'ticket_id');
}

// رابطه با ارجاع‌ها (Assignments)
public function assignments()
{
    return $this->hasMany(TaskAssignment::class, 'ticket_id');
}

// متد کمکی برای ثبت فعالیت (اگر قبلاً اضافه نکردید)
public function logActivity($description, $action = 'comment', $newStatus = null, $isInternal = false)
{
    return $this->activities()->create([
        'user_id' => auth()->id() ?? 1,
        'action' => $action,
        'description' => $description,
        'old_status_id' => $this->task_status_id,
        'new_status_id' => $newStatus,
        'is_internal' => $isInternal,
    ]);
}
// اضافه کردن این متد به مدل Ticket
public function taskStatus()
{
    return $this->belongsTo(TaskStatus::class, 'task_status_id');
}

// اگر رابطه‌ای برای سازنده تیکت ندارید، این را هم اضافه کنید
public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
}