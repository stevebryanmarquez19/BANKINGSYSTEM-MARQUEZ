<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Final URL: GET /api/dashboard/stats
Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
