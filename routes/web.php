<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// وارد کردن کامپوننت‌های Livewire
use App\Livewire\Provinces\ProvinceIndex;
use App\Livewire\Cities\CityIndex;
use App\Livewire\UnitTypes\UnitTypeIndex;
use App\Livewire\UnitTypes\UnitTypeHierarchyManager;
use App\Livewire\Units\UnitIndex;
use App\Livewire\Units\UnitTree;
use App\Livewire\Users\UserIndex;
use App\Livewire\Tickets\CreateTicket;
use App\Livewire\Tickets\TicketInbox;
use App\Livewire\Roles\RoleManager;

/*
|--------------------------------------------------------------------------
| روت‌های عمومی (Public Routes)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| روت‌های تحت نظارت سیستم احراز هویت (Authenticated Routes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // داشبورد اصلی
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // مدیریت پروفایل کاربر
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /* --- سیستم تیکتینگ (دسترسی عمومی کاربران لاگین شده) --- */
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/inbox', TicketInbox::class)->name('inbox');
        Route::get('/new', CreateTicket::class)->name('create');
    });

    /* |----------------------------------------------------------------------
    | روت‌های مدیریتی (Admin Only)
    | اینجا رول 'admin' را اعمال کرده‌ایم. 
    | اگر از Spatie استفاده می‌کنید، میان‌افزار 'role:admin' است.
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        
        // مدیریت سطوح دسترسی و نقش‌ها
        Route::get('/role-manager', RoleManager::class)->name('roles.manager');

        // مدیریت کاربران
        Route::get('/users', UserIndex::class)->name('users.index');

        // مدیریت تعاریف پایه (استان و شهر)
        Route::get('/provinces', ProvinceIndex::class)->name('provinces.index');
        Route::get('/cities', CityIndex::class)->name('cities.index');

        // مدیریت ساختار واحدها و سلسله مراتب
        Route::prefix('unit-types')->name('unit-types.')->group(function () {
            Route::get('/', UnitTypeIndex::class)->name('index');
            Route::get('/hierarchy', UnitTypeHierarchyManager::class)->name('hierarchy');
        });

        Route::prefix('units')->name('units.')->group(function () {
            Route::get('/', UnitIndex::class)->name('index');
            Route::get('/tree', UnitTree::class)->name('tree');
        });
    });
});

/*
|--------------------------------------------------------------------------
| روت‌های احراز هویت Breeze (Login, Register, Logout, ...)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';