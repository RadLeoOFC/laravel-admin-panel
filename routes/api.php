<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeskController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StripeWebhookController;

// API v1 routes
Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/desks', [DeskController::class, 'index']);
    Route::get('/memberships', [MembershipController::class, 'index']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/desks', [DeskController::class, 'store']);
        Route::get('/desks/{desk}', [DeskController::class, 'show']);
        Route::put('/desks/{desk}', [DeskController::class, 'update']);
        Route::delete('/desks/{desk}', [DeskController::class, 'destroy']);

        Route::post('/memberships', [MembershipController::class, 'store']);
        Route::get('/memberships/{membership}', [MembershipController::class, 'show']);
        Route::put('/memberships/{membership}', [MembershipController::class, 'update']);
        Route::delete('/memberships/{membership}', [MembershipController::class, 'destroy']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::post('/login', [AuthController::class, 'login']);
});

// Stripe Webhook
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);
