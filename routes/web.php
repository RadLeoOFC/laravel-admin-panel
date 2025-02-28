<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeskController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PaymentSettingsController;
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
    Route::view('/admin', 'layouts.admin')->name('admin.panel');

    // Product and category management (restricted to authenticated users)
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    
    // Desk and membership management
    Route::resource('desks', DeskController::class);
    Route::resource('memberships', MembershipController::class);
    
    Route::post('/memberships/{id}/extend', [MembershipController::class, 'extend'])->name('memberships.extend');
    Route::get('/memberships/{id}/extend', [MembershipController::class, 'showExtendForm'])->name('memberships.showExtendForm');

    // Routes for displaying the payment form and processing the payment
    Route::get('/payment/{membership_id}', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Route to handle incoming Stripe webhook events
    Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('webhook.stripe');


    Route::middleware('admin')->group(function () {
        // Added route for admin dashboard
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Additional membership-related route
        Route::post('/memberships/{id}/update-payment', [MembershipController::class, 'updatePaymentStatus'])->name('memberships.updatePayment');

        // Added route for reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::resource('payment_settings', PaymentSettingsController::class);
    });
});

// User profile management (restricted to authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include authentication routes (login, registration, etc.)
require __DIR__.'/auth.php';
