<?php

use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\HuntingBookingController;
use Illuminate\Support\Facades\Route;

Route::apiResource('guides', GuideController::class)
    ->only(['index']);

Route::apiResource('bookings', HuntingBookingController::class)
    ->only(['store']);
