<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Provinces\ProvinceIndex;
use App\Livewire\Cities\CityIndex;
use App\Livewire\UnitTypes\UnitTypeIndex;
use App\Livewire\Units\UnitIndex;
use App\Livewire\Units\UnitTree;
use App\Livewire\Users\UserIndex;
use App\Livewire\Tasks\TaskIndex;
use App\Livewire\Tickets\CreateTicket;

Route::get('/tickets/new', CreateTicket::class)->name('tickets.create');
Route::get('/tasks', TaskIndex::class);

Route::get('/users', UserIndex::class);

Route::get('/units/tree', UnitTree::class);

Route::get('/units', UnitIndex::class);

Route::get('/unit-types', UnitTypeIndex::class);

Route::get('/cities', CityIndex::class);

Route::get('/provinces', ProvinceIndex::class);

Route::get('/', function () {
    return view('welcome');
});
