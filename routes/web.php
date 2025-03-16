<?php

use App\Http\Controllers\Member\DependentController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\PaymentGatewayController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test route to display the current locale
Route::get('/locale', function () {
    return 'Current locale: '.app()->getLocale();
});

// Member Routes
Route::middleware(['auth', 'verified'])->prefix('member')->name('member.')->group(function () {
    Route::get('/profile', [MemberController::class, 'show'])->name('show');
    Route::get('/edit', [MemberController::class, 'edit'])->name('edit');
    Route::patch('/', [MemberController::class, 'update'])->name('update');
    Route::get('/change-password', [MemberController::class, 'showChangePasswordForm'])->name('change-password');
    Route::patch('/change-password', [MemberController::class, 'changePassword'])->name('update-password');
});

// Dependent Routes
Route::middleware(['auth', 'verified'])->prefix('dependent')->name('dependent.')->group(function () {
    Route::get('/', [DependentController::class, 'index'])->name('index');
    Route::get('/create', [DependentController::class, 'create'])->name('create');
    Route::post('/', [DependentController::class, 'store'])->name('store');
    Route::get('/{dependent}/edit', [DependentController::class, 'edit'])->name('edit');
    Route::patch('/{dependent}', [DependentController::class, 'update'])->name('update');
    Route::delete('/{dependent}', [DependentController::class, 'destroy'])->name('destroy');
});

// Payment Routes
Route::middleware(['auth', 'verified'])->prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/create', [PaymentController::class, 'create'])->name('create');
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
    Route::post('/{payment}/verify', [PaymentController::class, 'verify'])->name('verify');
    Route::get('/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('receipt');
});

// Payment Gateway Callback Routes
Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
    Route::get('/callback', [PaymentGatewayController::class, 'handleCallback'])->name('callback');
    Route::post('/webhook', [PaymentGatewayController::class, 'handleWebhook'])->name('webhook');
});

// Registration Page - This uses the Livewire component
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

// Receipt routes
Route::middleware(['auth'])->group(function () {
    Route::get('/receipts', [App\Http\Controllers\Payment\ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{receipt}', [App\Http\Controllers\Payment\ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/download', [App\Http\Controllers\Payment\ReceiptController::class, 'download'])->name('receipts.download');
    
    // Admin only routes
    Route::middleware(['can:generate,App\Models\Receipt'])->group(function () {
        Route::post('/payments/{payment}/generate-receipt', [App\Http\Controllers\Payment\ReceiptController::class, 'generate'])->name('receipts.generate');
    });
});

require __DIR__.'/auth.php';
