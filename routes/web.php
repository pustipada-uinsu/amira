<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminVenueController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminEventAdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSessionController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\EventMaterialController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->middleware('throttle:6,1')->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->middleware('throttle:6,1');

Route::get('/post-login', function () {
    return redirect()->route('dashboard');
})->name('post.login.redirect')->middleware('auth');

Route::middleware(['auth', 'active', 'log'])->group(function () {

    Route::get('/profile/complete', [ProfileController::class, 'create'])
        ->name('profile.complete');

    Route::post('/profile/complete', [ProfileController::class, 'store'])
        ->name('profile.store');

    Route::get('/materials/{id}/{index}', [EventMaterialController::class, 'download'])->name('materials.download');
});


Route::middleware(['auth', 'profile.complete', 'active', 'log'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::get('/detail-event/{event}', [EventController::class, 'detail'])->name('events.detail');

    Route::post('/apply-event/{event}', [EventRegistrationController::class, 'apply'])->name('events.apply');

    Route::get('/scan', [AttendanceController::class, 'scanPage'])->name('attendance.scan');
    Route::post('/scan', [AttendanceController::class, 'scanProcess'])->name('attendance.scan.store');
    Route::post('/attendance/event-scan', [CheckinController::class, 'scanEventCheckin'])->name('attendance.checkin');
});


Route::middleware(['auth', 'role:adminsuper', 'active', 'log'])->group(function () {

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/suspend', [AdminUserController::class, 'toggleSuspend'])->name('users.suspend');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}', [AdminUserController::class, 'update']);

    Route::get('/events/{event}/admins', [AdminEventAdminController::class, 'index'])->name('ea.index');
    Route::post('/events/{event}/admins', [AdminEventAdminController::class, 'store'])->name('ea.store');
    Route::put('/events/{event}/admins/{user}', [AdminEventAdminController::class, 'update'])->name('ea.update');
    Route::delete('/events/{event}/admins/{user}', [AdminEventAdminController::class, 'destroy'])->name('ea.destroy');

    Route::get('/admin/profiles', [AdminProfileController::class, 'index'])->name('admin.profiles');
    Route::get('/admin/profiles/export', [AdminProfileController::class, 'export'])->name('admin.profiles.export');
});

Route::middleware(['auth', 'role:admin,adminsuper', 'active', 'log'])->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::post('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::prefix('events/{event}')->group(function () {
        Route::get('/sessions', [EventSessionController::class, 'index'])->name('events.sessions.index');
        Route::post('/sessions', [EventSessionController::class, 'store'])->name('events.sessions.store');
        Route::put('/sessions/{session}', [EventSessionController::class, 'update'])->name('events.sessions.update');
        Route::delete('/sessions/{session}', [EventSessionController::class, 'destroy'])->name('events.sessions.destroy');
    });

    Route::get('/events/{event}/participants', [EventRegistrationController::class, 'index'])->name('participant.index');
    Route::post('/events/{event}/participants', [EventRegistrationController::class, 'store'])->name('participant.store');
    Route::post('/participants/{id}/approve', [EventRegistrationController::class, 'approve'])->name('participant.approve');
    Route::post('/participants/{id}/unapprove', [EventRegistrationController::class, 'unapprove'])->name('participant.approve');;
    Route::post('/participants/{id}/reject', [EventRegistrationController::class, 'reject'])->name('participant.reject');

    Route::get('/events/{event}/available-users', [EventRegistrationController::class, 'availableUsers']);

    Route::get('/events/{event}/participants/export', [EventRegistrationController::class, 'export'])->name('participant.export');

    Route::get('/venue', [AdminVenueController::class, 'index'])->name('venue.index');
    Route::post('/venue', [AdminVenueController::class, 'store'])->name('venue.store');
    Route::put('/venue/{venue}', [AdminVenueController::class, 'update'])->name('venue.update');
    Route::delete('/venue/{venue}', [AdminVenueController::class, 'destroy'])->name('venue.destroy');

    Route::get('/events/{event}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/events/{event}/attendance/participants', [AttendanceController::class, 'participants']);
    Route::get('/events/{event}/attendance/qr', [AttendanceController::class, 'generateQR']);
    Route::get('/events/{event}/attendance/qr-page', [AttendanceController::class, 'qrPage']);

    Route::get('/events/{event}/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::post('/events/{event}/attendance/manual', [AttendanceController::class, 'manualCheckin'])->name('attendance.manual');

    Route::get('/events/{event}/checkin-qr', [CheckinController::class, 'eventQRPage']);
    Route::get('/events/{event}/generate-checkin-qr', [CheckinController::class, 'generateEventQR']);
    Route::get('/events/{event}/checkin-participants', [CheckinController::class, 'participants']);
    Route::post('/events/{event}/checkin-manual', [CheckinController::class, 'manualCheckin']);
    Route::get('/events/{event}/checkin-export', [CheckinController::class, 'exportEventCheckin']);
});



require __DIR__ . '/auth.php';
