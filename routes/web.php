<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/calculate-route', function () {
    return view('calculate');
})->name('calculate.route');

Route::get('/admin/settings', function () {
    return view('admin.settings');
});

Route::get('/admin/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/admin/settings', [SettingController::class, 'update'])->name('settings.update');

Route::post('/create_order', [OrderController::class, 'createOrder'])->name('create_order');

