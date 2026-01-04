<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['task_id','user_id' ,'file_path', 'file_name', 'file_size','ticket_id'];

public function task()
{
    return $this->belongsTo(Task::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}
public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
