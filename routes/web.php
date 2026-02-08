<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Provinces\ProvinceIndex;
use App\Livewire\Cities\CityIndex;
use App\Livewire\UnitTypes\UnitTypeIndex;
use App\Livewire\Units\UnitIndex;
use App\Livewire\Units\UnitTree;
use App\Livewire\Users\UserIndex;
use App\Livewire\Tickets\CreateTicket;
use App\Livewire\Tickets\TicketInbox;
use App\Livewire\Roles\RoleManager;

Route::get('/role-manager', RoleManager::class)->name('roles.manager');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/inbox-tickets', TicketInbox::class)->name('tickets.inbox');

Route::get('/tickets/new', CreateTicket::class)->name('tickets.create');

Route::get('/users', UserIndex::class);

Route::get('/units/tree', UnitTree::class);

Route::get('/units', UnitIndex::class);

Route::get('/unit-types', UnitTypeIndex::class);

Route::get('/cities', CityIndex::class);

Route::get('/provinces', ProvinceIndex::class);

// Route::get('/', function () {
//     return view('welcome');
// });

require __DIR__.'/auth.php';
