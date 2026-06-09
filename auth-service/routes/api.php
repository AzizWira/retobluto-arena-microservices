<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'service' => 'auth-service',
        'message' => 'Auth Service is running',
    ]);
});

Route::post('/admin/login', [AuthController::class, 'adminLogin']);

Route::post('/member/login', [AuthController::class, 'memberLogin']);
Route::post('/member/register/request-otp', [AuthController::class, 'requestMemberOtp']);
Route::post('/member/register/verify', [AuthController::class, 'verifyMemberOtp']);
Route::post('/member/register/resend-otp', [AuthController::class, 'resendMemberOtp']);

Route::post('/validate-token', [AuthController::class, 'validateToken']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/admin/members', [AuthController::class, 'adminCreateMember']);
    Route::delete('/admin/members/auth-account', [AuthController::class, 'adminDeleteMemberAuthAccount']);
});
