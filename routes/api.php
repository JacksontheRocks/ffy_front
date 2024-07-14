<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;

Route::middleware('api')->get('/settings', [SettingController::class, 'getSettings']);
