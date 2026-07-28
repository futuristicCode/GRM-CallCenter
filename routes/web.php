<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TypeReclamationController;
use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Réclamations
    Route::get('/reclamations', [ReclamationController::class, 'index'])->name('reclamations.index');
    Route::get('/reclamations/create', [ReclamationController::class, 'create'])->name('reclamations.create');
    Route::post('/reclamations', [ReclamationController::class, 'store'])->name('reclamations.store');
    Route::get('/reclamations/{reclamation}', [ReclamationController::class, 'show'])->name('reclamations.show');
    Route::get('/reclamations/{reclamation}/edit', [ReclamationController::class, 'edit'])->name('reclamations.edit');
    Route::put('/reclamations/{reclamation}', [ReclamationController::class, 'update'])->name('reclamations.update');
    Route::delete('/reclamations/{reclamation}', [ReclamationController::class, 'destroy'])->name('reclamations.destroy');

    // Actions sur réclamation
    Route::post('/reclamations/{reclamation}/prendre-en-charge', [ReclamationController::class, 'prendreEnCharge'])->name('reclamations.prendre-en-charge');
    Route::post('/reclamations/{reclamation}/changer-statut', [ReclamationController::class, 'changerStatut'])->name('reclamations.changer-statut');
    Route::post('/reclamations/{reclamation}/assigner', [ReclamationController::class, 'assigner'])->name('reclamations.assigner');
    Route::get('/api/sous-types', [ReclamationController::class, 'sousTypes'])->name('api.sous-types');

    // Messages
    Route::post('/reclamations/{reclamation}/messages', [MessageController::class, 'store'])->name('messages.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Types de réclamation
    Route::get('/types', [TypeReclamationController::class, 'index'])->name('types.index');
    Route::post('/types', [TypeReclamationController::class, 'store'])->name('types.store');
    Route::put('/types/{type}', [TypeReclamationController::class, 'update'])->name('types.update');
    Route::delete('/types/{type}', [TypeReclamationController::class, 'destroy'])->name('types.destroy');
    Route::post('/types/sous-types', [TypeReclamationController::class, 'storeSousType'])->name('types.sous-types.store');
    Route::delete('/sous-types/{sousType}', [TypeReclamationController::class, 'destroySousType'])->name('types.sous-types.destroy');

    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

require __DIR__.'/auth.php';
