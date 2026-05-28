<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\SpecialistPortalController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect('/login');
    }

    return auth()->user()?->isSpecialist()
        ? redirect('/mi-panel')
        : redirect('/agenda');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/mi-panel', [SpecialistPortalController::class, 'index'])->name('specialist.portal');

    Route::get('/chat', function () {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return redirect()->route('conversations.index');
    });

    Route::get('/agenda', function () {
        return view('agenda');
    })->name('agenda');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report.pdf', [DashboardController::class, 'exportMonthlyPdf'])->name('dashboard.report.pdf');

    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/summary', [AlertController::class, 'summary'])->name('alerts.summary');
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'read'])->name('alerts.read');
    Route::patch('/alerts/read-all', [AlertController::class, 'readAll'])->name('alerts.read-all');

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

    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');
    Route::get('/offers/{offer}/edit', [OfferController::class, 'edit'])->name('offers.edit');
    Route::patch('/offers/{offer}', [OfferController::class, 'update'])->name('offers.update');
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::patch('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    Route::get('/api/calendar-events', [BookingController::class, 'getCalendarEvents']);
    Route::get('/api/specialists/options', [BookingController::class, 'specialistsOptions']);
    Route::patch('/api/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::patch('/api/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::patch('/api/bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::patch('/api/bookings/{booking}/assign-specialist', [BookingController::class, 'assignSpecialist']);
    Route::delete('/api/bookings/{booking}', [BookingController::class, 'destroy']);

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
