<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    protected $fillable = ['title'];

    // هر وضعیت می‌تواند تسک‌های زیادی داشته باشد
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}