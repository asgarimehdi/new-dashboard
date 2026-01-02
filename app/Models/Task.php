<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'unit_id',
        'created_by',
        'task_status_id',
        'priority', 
        'due_date', 
        'attachment_path'
    ];
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 
// فیلدهایی که باید به صورت تاریخ باشند
    protected $casts = [
        'due_date' => 'datetime',
    ];

    // ۱. تبدیل تاریخ ایجاد به شمسی (Attribute)
    public function getShamsiCreatedAttribute()
    {
        return Verta::instance($this->created_at)->format('Y/m/d H:i');
    }

    // ۲. تبدیل مهلت انجام به شمسی
    public function getShamsiDueDateAttribute()
    {
        return $this->due_date ? Verta::instance($this->due_date)->format('Y/m/d') : 'تعیین نشده';
    }

    // ۳. منطق رنگی برای ددلاین (باقی‌مانده ۳ روز)
   public function getDeadlineStatusAttribute()
{
    if (!$this->due_date) return 'normal';
    
    // مقایسه تاریخ امروز با مهلت
    $today = now()->startOfDay();
    $due = \Carbon\Carbon::parse($this->due_date)->startOfDay();
    $diff = $today->diffInDays($due, false);

    if ($diff < 0) return 'expired'; // تاریخ گذشته
    if ($diff <= 3) return 'urgent'; // ۳ روز یا کمتر مانده
    return 'normal';
}
    // رابطه با ارجاعات برای پیدا کردن آخرین ارجاع
   

    public function lastAssignment()
    {
        return $this->hasOne(TaskAssignment::class)->latestOfMany();
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }
    public function activities()
{
    return $this->hasMany(TaskActivity::class);
}

}
