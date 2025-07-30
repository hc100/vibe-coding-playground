<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return redirect()->route('logs.calendar');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // ログ関連ルート
    Route::resource('logs', LogController::class);
    Route::get('/calendar', [LogController::class, 'calendar'])->name('logs.calendar');
    Route::get('/logs/date/{date}', [LogController::class, 'byDate'])->name('logs.by-date');
    
    // カレンダーAPI（CSRF除外）
    Route::get('/api/calendar/events', [LogController::class, 'calendarEvents'])->name('api.calendar.events');
    
    // タグ関連ルート  
    Route::resource('tags', TagController::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';