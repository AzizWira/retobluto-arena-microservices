<?php

use App\Http\Controllers\Api\FieldController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'service' => 'field-service',
        'message' => 'Field Service is running',
    ]);
});

Route::get('/fields/available', [FieldController::class, 'available']);

Route::get('/fields/dashboard-stats', [FieldController::class, 'dashboardStats']);
Route::get('/fields', [FieldController::class, 'index']);
Route::post('/fields', [FieldController::class, 'store']);

Route::get('/fields/{id}/detail', [FieldController::class, 'detail']);
Route::get('/fields/{id}/booking-schedule', [FieldController::class, 'bookingSchedule']);
Route::patch('/fields/{id}/status', [FieldController::class, 'updateStatus']);

Route::get('/fields/{id}', [FieldController::class, 'show']);
Route::put('/fields/{id}', [FieldController::class, 'update']);
Route::delete('/fields/{id}', [FieldController::class, 'destroy']);
