<?php

use App\Http\Controllers\Web\AuthPageController;
use App\Http\Controllers\Web\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\FieldController as AdminFieldController;
use App\Http\Controllers\Web\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Web\Admin\NotificationController as AdminNotificationController;

use App\Http\Controllers\Web\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Web\Member\FieldController as MemberFieldController;
use App\Http\Controllers\Web\Member\BookingController as MemberBookingController;
use App\Http\Controllers\Web\Member\ProfileController as MemberProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!session()->has('access_token')) {
        return redirect()->route('login.member');
    }

    return session('user.role') === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('member.home');
});

Route::get('/dashboard', function () {
    if (!session()->has('access_token')) {
        return redirect()->route('login.member');
    }

    return session('user.role') === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('member.home');
});

Route::get('/login', [AuthPageController::class, 'memberLoginPage'])->name('login.member');
Route::post('/login', [AuthPageController::class, 'memberLogin'])->name('login.member.submit');

Route::get('/register', [AuthPageController::class, 'registerPage'])->name('register.member');
Route::post('/register/request-otp', [AuthPageController::class, 'requestOtp'])->name('register.requestOtp');

Route::get('/verify-otp', [AuthPageController::class, 'verifyOtpPage'])->name('register.verifyOtpPage');
Route::post('/verify-otp', [AuthPageController::class, 'verifyOtp'])->name('register.verifyOtp');
Route::post('/verify-otp/resend', [AuthPageController::class, 'resendOtp'])->name('register.resendOtp');

Route::get('/login/master', [AuthPageController::class, 'adminLoginPage'])->name('login.admin');
Route::post('/login/master', [AuthPageController::class, 'adminLogin'])->name('login.admin.submit');

Route::post('/logout', [AuthPageController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/fields', [AdminFieldController::class, 'index'])->name('fields.index');
    Route::get('/fields/create', [AdminFieldController::class, 'create'])->name('fields.create');
    Route::post('/fields', [AdminFieldController::class, 'store'])->name('fields.store');
    Route::get('/fields/{id}', [AdminFieldController::class, 'show'])->name('fields.show');
    Route::get('/fields/{id}/edit', [AdminFieldController::class, 'edit'])->name('fields.edit');
    Route::put('/fields/{id}', [AdminFieldController::class, 'update'])->name('fields.update');
    Route::patch('/fields/{id}/status', [AdminFieldController::class, 'updateStatus'])->name('fields.status');
    Route::delete('/fields/{id}', [AdminFieldController::class, 'destroy'])->name('fields.destroy');

    Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [AdminMemberController::class, 'create'])->name('members.create');
    Route::post('/members', [AdminMemberController::class, 'store'])->name('members.store');
    Route::get('/members/{id}', [AdminMemberController::class, 'show'])->name('members.show');
    Route::get('/members/{id}/edit', [AdminMemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id}', [AdminMemberController::class, 'update'])->name('members.update');
    Route::patch('/members/{id}/status', [AdminMemberController::class, 'updateStatus'])->name('members.status');
    Route::delete('/members/{id}', [AdminMemberController::class, 'destroy'])->name('members.destroy');

    Route::get('/booking-requests', [AdminBookingController::class, 'requests'])->name('bookings.requests');
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{id}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');

    Route::get('/notifications/send-email', [AdminNotificationController::class, 'createEmail'])->name('notifications.createEmail');
    Route::post('/notifications/send-email', [AdminNotificationController::class, 'sendEmail'])->name('notifications.sendEmail');

    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [AdminNotificationController::class, 'show'])->name('notifications.show');
});

Route::prefix('member')->name('member.')->group(function () {
    Route::get('/home', [MemberDashboardController::class, 'index'])->name('home');

    Route::get('/fields', [MemberFieldController::class, 'index'])->name('fields.index');
    Route::get('/fields/{id}', [MemberFieldController::class, 'show'])->name('fields.show');

    Route::get('/bookings', [MemberBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [MemberBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [MemberBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [MemberBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/cancel', [MemberBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/profile', [MemberProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [MemberProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
});
