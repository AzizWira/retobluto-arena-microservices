<?php

use App\Http\Controllers\Api\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'service' => 'member-service',
        'message' => 'Member Service is running',
    ]);
});

Route::get('/profile', [MemberController::class, 'profile']);
Route::put('/profile', [MemberController::class, 'updateProfile']);

Route::get('/members/dashboard-stats', [MemberController::class, 'dashboardStats']);
Route::get('/members', [MemberController::class, 'index']);
Route::post('/members', [MemberController::class, 'store']);

Route::get('/members/user/{userId}', [MemberController::class, 'getByUserId']);
Route::patch('/members/{id}/status', [MemberController::class, 'updateStatus']);

Route::get('/members/{id}', [MemberController::class, 'show']);
Route::put('/members/{id}', [MemberController::class, 'update']);
Route::delete('/members/{id}', [MemberController::class, 'destroy']);

Route::post('/internal/members/sync-from-auth', [MemberController::class, 'syncFromAuth']);
Route::get('/internal/members/{id}', [MemberController::class, 'internalShow']);
Route::get('/internal/members/user/{userId}', [MemberController::class, 'internalGetByUserId']);
