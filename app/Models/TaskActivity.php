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
    'is_internal'
];

   

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
