<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_code', 'user_id', 'unit_id', 'subject', 
        'content', 'priority', 'status', 'task_id','current_assignee_id',
    'accepted_at',
    'completed_at',
    ];
// تعریف status_name برای نمایش فارسی وضعیت‌های تیکت
public function getStatusNameAttribute()
{
    return match($this->status) {
        'created' => 'جدید (واحد)',
        'forwarded' => 'ارجاع شده',
        'accepted' => 'در حال پیگیری',
        'closed' => 'پایان یافته',
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
        return $this->belongsTo(User::class , 'user_id');
    }

    // رابطه با شخصی که تیکت به او واگذار شده
public function assignee(): BelongsTo
{
    return $this->belongsTo(User::class, 'current_assignee_id');
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



// متد کمکی برای ثبت فعالیت (اگر قبلاً اضافه نکردید)
public function logActivity($description, $action = 'comment', $newStatus = null, $isInternal = false)
{
    return $this->activities()->create([
        'user_id' => auth()->id() ?? 1,
        'action' => $action,
        'description' => $description,
       
        'is_internal' => $isInternal,
    ]);
}
}