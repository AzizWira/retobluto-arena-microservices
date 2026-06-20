<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'service' => 'notification-service',
        'message' => 'Notification Service is running',
    ]);
});

Route::get('/notifications/dashboard-stats', [NotificationController::class, 'dashboardStats']);
Route::get('/notifications/logs', [NotificationController::class, 'logs']);
Route::get('/notifications/logs/{id}', [NotificationController::class, 'showLog']);

Route::post('/notifications/send-email', [NotificationController::class, 'sendEmail']);
Route::post('/notifications/send-otp', [NotificationController::class, 'sendOtp']);

Route::post('/internal/notifications/otp', [NotificationController::class, 'internalOtp']);
Route::post('/internal/notifications/booking-status', [NotificationController::class, 'internalBookingStatus']);
