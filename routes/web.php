<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserOtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordOtpController;
use App\Http\Controllers\ContributionController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create/{contribution_id}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments/store', [PaymentController::class, 'store'])->name('payments.store');

    Route::post('/paypal/payment', [PayPalController::class, 'createPayment'])->name('paypal.payment');
    Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

    Route::get('contributions/user-contribution-list', [ContributionController::class, 'userContributionList'])->name('contributions.userList');
    Route::get('contributions/my-contributions', [ContributionController::class, 'userContributions'])->name('contributions.userContributions');

    Route::get('users/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('users/profileData', [UserController::class, 'myProfileData'])->name('users.profileData');

    Route::post('/otp/send', [UserOtpController::class, 'send'])->name('otp.send');
    Route::post('/otp/verify', [UserOtpController::class, 'verify'])->name('otp.verify');
    Route::post('/password/update', [UserOtpController::class, 'updatePassword'])->name('user.password.update');
    Route::post('/users/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');
});

Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('users/store', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('users/update', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('users/destroy/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');


    Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::get('contributions/create', [ContributionController::class, 'create'])->name('contributions.create');
    Route::post('contributions/store', [ContributionController::class, 'store'])->name('contributions.store');

    Route::get('admin/users', [UserController::class, 'list'])->name('admin.users.list');
    Route::get('payments/list', [PaymentController::class, 'list'])->name('payments.list');
    Route::get('contributions/list', [ContributionController::class, 'list'])->name('contributions.list');

    Route::get('contributions/edit/{contribution_id}', [ContributionController::class, 'edit'])->name('contributions.edit');
    Route::post('contributions/update', [ContributionController::class, 'update'])->name('contributions.update');
    Route::post('contributions/destroy/{contribution_id}', [ContributionController::class, 'destroy'])->name('contributions.destroy');

    Route::get('payments/edit/{payment_id}', [PaymentController::class, 'edit'])->name('admin.payments.edit');
    Route::post('payments/update', [PaymentController::class, 'update'])->name('admin.payments.update');
    Route::post('payments/destroy/{payment_id}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');

    Route::get('/admin/dashboard-data', [DashboardController::class, 'index'])->name('admin.dashboard');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
