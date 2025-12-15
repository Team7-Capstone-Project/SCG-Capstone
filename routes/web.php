<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard with OTD Metrics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shipment Management
    Route::get('shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');
    Route::resource('shipments', ShipmentController::class);
    Route::post('shipments/{shipment}/update-status', [ShipmentController::class, 'updateStatus'])
        ->name('shipments.update-status')
        ->middleware('can:updateStatus,shipment');

    // Master Data Management
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

