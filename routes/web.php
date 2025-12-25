<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Provinces\ProvinceIndex;

Route::get('/provinces', ProvinceIndex::class);

Route::get('/', function () {
    return view('welcome');
});
