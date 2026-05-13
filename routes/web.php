<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTrackingController;
use App\Http\Controllers\Admin\CarInventoryController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Staff\StaffCarInventoryController;
use App\Http\Controllers\Staff\StaffTrackingController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\ThemeController;
use Illuminate\Support\Facades\Route;

// Root — smart redirect based on auth state
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect('/admin/dashboard'),
        'staff' => redirect('/staff/tracking'),
        'customer' => redirect('/customer/booking'),
        default => redirect('/login'),
    };
})->middleware('auth')->name('dashboard');

// ----- AUTH ROUTES (provided by Breeze) -----
require __DIR__.'/auth.php';

// ----- ADMIN ROUTES -----
Route::prefix('admin')->middleware(['auth', 'role:admin', 'theme'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Tracking
    Route::get('/tracking',                        [AdminTrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/{booking}/confirm-pay', [AdminTrackingController::class, 'confirmPayment'])->name('tracking.confirmPayment');
    Route::post('/tracking/{booking}/return',      [AdminTrackingController::class, 'markReturn'])->name('tracking.markReturn');
    Route::post('/tracking/{booking}/cancel',      [AdminTrackingController::class, 'cancel'])->name('tracking.cancel');

    // Car Inventory
    Route::get('/car-inventory',          [CarInventoryController::class, 'index'])->name('car-inventory');
    Route::post('/car-inventory',         [CarInventoryController::class, 'store'])->name('car-inventory.store');
    Route::post('/car-inventory/{car}',   [CarInventoryController::class, 'update'])->name('car-inventory.update');
    Route::delete('/car-inventory/{car}', [CarInventoryController::class, 'destroy'])->name('car-inventory.destroy');

    // Report
    Route::get('/report',        [ReportController::class, 'index'])->name('report');
    Route::get('/report/export', [ReportController::class, 'export'])->name('report.export');

    // Manage Users
    Route::get('/manage-users',                           [ManageUsersController::class, 'index'])->name('manage-users');
    Route::post('/manage-users',                          [ManageUsersController::class, 'store'])->name('manage-users.store');
    Route::post('/manage-users/{user}/toggle-status',     [ManageUsersController::class, 'toggleStatus'])->name('manage-users.toggleStatus');
    Route::delete('/manage-users/{user}',                 [ManageUsersController::class, 'destroy'])->name('manage-users.destroy');
});

// ----- STAFF ROUTES -----
Route::prefix('staff')->middleware(['auth', 'role:staff', 'theme'])->name('staff.')->group(function () {
    Route::get('/tracking',                        [StaffTrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/{booking}/confirm-pay', [StaffTrackingController::class, 'confirmPayment'])->name('tracking.confirmPayment');
    Route::post('/tracking/{booking}/return',      [StaffTrackingController::class, 'markReturn'])->name('tracking.markReturn');
    Route::post('/tracking/{booking}/cancel',      [StaffTrackingController::class, 'cancel'])->name('tracking.cancel');
    Route::get('/car-inventory',                   [StaffCarInventoryController::class, 'index'])->name('car-inventory');
});

// ----- CUSTOMER ROUTES -----
Route::prefix('customer')->middleware(['auth', 'role:customer', 'theme'])->name('customer.')->group(function () {
    Route::get('/booking',                       [BookingController::class, 'create'])->name('booking');
    Route::post('/booking',                      [BookingController::class, 'store'])->name('booking.store');
    Route::get('/my-bookings',                   [BookingController::class, 'myBookings'])->name('my-bookings');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('my-bookings.cancel');
    Route::post('/theme',                        [ThemeController::class, 'update'])->name('theme');
});
