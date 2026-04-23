<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SpecialistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/agenda') : redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/chat', function () {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return redirect()->route('conversations.index');
    });

    Route::get('/agenda', function () {
        return view('agenda');
    });

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/summaries', [ConversationController::class, 'summaries'])->name('conversations.summaries');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages'])->name('conversations.messages.index');
    Route::patch('/conversations/{conversation}/pause', [ConversationController::class, 'pause'])->name('conversations.pause');
    Route::patch('/conversations/{conversation}/resume', [ConversationController::class, 'resume'])->name('conversations.resume');
    Route::patch('/conversations/{conversation}/take-over', [ConversationController::class, 'takeOver'])->name('conversations.take-over');
    Route::patch('/conversations/{conversation}/release', [ConversationController::class, 'release'])->name('conversations.release');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('conversations.messages.store');

    Route::get('/specialists', [SpecialistController::class, 'index'])->name('specialists.index');
    Route::get('/specialists/create', [SpecialistController::class, 'create'])->name('specialists.create');
    Route::post('/specialists', [SpecialistController::class, 'store'])->name('specialists.store');
    Route::get('/specialists/{specialist}/edit', [SpecialistController::class, 'edit'])->name('specialists.edit');
    Route::patch('/specialists/{specialist}', [SpecialistController::class, 'update'])->name('specialists.update');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::patch('/services/{service}', [ServiceController::class, 'update'])->name('services.update');

    Route::get('/api/calendar-events', [BookingController::class, 'getCalendarEvents']);
    Route::patch('/api/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::patch('/api/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::patch('/api/bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
