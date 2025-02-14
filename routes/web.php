<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeskController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Home page route
Route::get('/', function () {
    return view('welcome');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (only for authenticated users)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin panel route
    Route::view('/admin', 'layouts.admin')->name('admin.dashboard');

    // Product and category management (restricted to authenticated users)
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    
    // Desk and membership management
    Route::resource('desks', DeskController::class);
    Route::resource('memberships', MembershipController::class);

    // Additional membership-related routes
    Route::post('/memberships/{id}/extend', [MembershipController::class, 'extend'])->name('memberships.extend');
    Route::post('/memberships/{id}/update-payment', [MembershipController::class, 'updatePaymentStatus'])->name('memberships.updatePayment');

    // 📌 Added route for reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// User profile management (restricted to authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include authentication routes (login, registration, etc.)
require __DIR__.'/auth.php';
