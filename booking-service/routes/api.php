<?php

use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [BookingController::class, 'health']);

Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/dashboard-stats', [BookingController::class, 'dashboardStats']);
Route::get('/bookings', [BookingController::class, 'index']);

Route::get('/member/bookings', [BookingController::class, 'memberBookings']);
Route::get('/member/bookings/history', [BookingController::class, 'memberHistory']);
Route::post('/member/bookings/{id}/cancel', [BookingController::class, 'cancel']);

Route::get('/admin/booking-requests', [BookingController::class, 'adminRequests']);
Route::post('/admin/bookings/{id}/approve', [BookingController::class, 'approve']);
Route::post('/admin/bookings/{id}/reject', [BookingController::class, 'reject']);

Route::get('/bookings/field/{fieldId}/schedule', [BookingController::class, 'fieldSchedule']);
Route::get('/bookings/member/{memberId}', [BookingController::class, 'bookingsByMember']);

Route::get('/bookings/{id}', [BookingController::class, 'show']);
