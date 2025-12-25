<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UnitTypeHierarchy extends Model
{
    protected $fillable = [
        'parent_unit_type_id',
        'child_unit_type_id',
    ];
}
