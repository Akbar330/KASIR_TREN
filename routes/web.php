<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('transactions/cancel-requests', [TransactionController::class, 'cancelRequests'])->name('transactions.cancel-requests');
Route::post('transactions/{id}/approve-cancel', [TransactionController::class, 'approveCancel'])->name('transactions.approve-cancel');


Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi - Admin & Kasir
    Route::resource('transactions', TransactionController::class);
    Route::post('transactions/check-availability', [TransactionController::class, 'checkAvailability'])->name('transactions.check-availability');
    Route::get('transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');
    Route::put('transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');

    // Diskon - Admin & Kasir
    Route::resource('discounts', DiscountController::class);

    // Booking - Admin & Kasir
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');

    // Customer - Admin & Kasir
    Route::resource('customers', CustomerController::class);
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // Product - Admin & Kasir
    Route::resource('products', ProductController::class);
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');

    // Lapangan - Admin & Kasir
    Route::resource('lapangan', LapanganController::class);

    // Routes khusus Admin
    Route::middleware(['role:Admin'])->group(function () {
        // Reports
        Route::get('reports/omset', [ReportController::class, 'omset'])->name('reports.omset');
        Route::get('reports/booking', [ReportController::class, 'booking'])->name('reports.booking');
        Route::get('reports/product', [ReportController::class, 'product'])->name('reports.product');
        Route::get('reports/export-omset', [ReportController::class, 'exportOmset'])->name('reports.export-omset');

        Route::get('transactions/cancel-requests', [TransactionController::class, 'cancelRequests'])->name('transactions.cancel-requests');
        Route::post('transactions/{id}/approve-cancel', [TransactionController::class, 'approveCancel'])->name('transactions.approve-cancel');

        // User Management
        Route::resource('users', UserController::class);
    });
});
