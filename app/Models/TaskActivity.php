<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
  protected $fillable = [
    'ticket_id', 
    'user_id',
    'action',
    'description',
    'old_status_id',
    'new_status_id',
    'is_internal'
];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'new_status_id');
    }
}
