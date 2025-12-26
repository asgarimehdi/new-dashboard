<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'unit_id',
        'created_by',
        'task_status_id',
    ];

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
