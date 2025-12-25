<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Provinces\ProvinceIndex;
use App\Livewire\Cities\CityIndex;

Route::get('/cities', CityIndex::class);

Route::get('/provinces', ProvinceIndex::class);

Route::get('/', function () {
    return view('welcome');
});
