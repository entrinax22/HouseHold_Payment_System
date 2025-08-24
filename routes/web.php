<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\PaypalController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');



Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create/{contribution_id}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments/store', [PaymentController::class, 'store'])->name('payments.store');

    Route::post('/paypal/payment', [PayPalController::class, 'createPayment'])->name('paypal.payment');
    Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

    Route::get('contributions/user-contribution-list', [ContributionController::class, 'userContributionList'])->name('contributions.userList');
    Route::get('contributions/my-contributions', [ContributionController::class, 'userContributions'])->name('contributions.userContributions');

});

Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::get('contributions/create', [ContributionController::class, 'create'])->name('contributions.create');
    Route::post('contributions/store', [ContributionController::class, 'store'])->name('contributions.store');

    Route::get('admin/users', [UserController::class, 'list'])->name('admin.users.list');
    Route::get('payments/list', [PaymentController::class, 'list'])->name('payments.list');
    Route::get('contributions/list', [ContributionController::class, 'list'])->name('contributions.list');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
