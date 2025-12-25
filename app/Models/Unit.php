<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'unit_type_id',
        'parent_id',
        'city_id',
        'is_active'
    ];

    public function type()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }
    public function childrenRecursive()
{
    return $this->children()->with('childrenRecursive');
}

}

