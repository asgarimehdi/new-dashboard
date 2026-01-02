<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TaskComment extends Model
{
    protected $fillable = ['task_id','user_id','content'];
    use HasFactory;

  

    // رابطه با مدل تسک
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // رابطه با مدل کاربر (این همان بخشی است که فراموش شده بود)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
