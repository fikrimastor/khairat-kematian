<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\Member\DependentController;
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

// Payment Routes
// Route::middleware(['auth', 'verified'])->prefix('payments')->name('payments.')->group(function () {
Route::group([
    'middleware' => ['auth', 'verified'],
    'prefix' => 'payments',
    'as' => 'payments.',
    'controller' => App\Http\Controllers\Payment\PaymentController::class,
], function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::get('/{payment}', 'show')->name('show');
    Route::post('/{payment}/verify', 'verify')
        ->middleware('permission:payment.verify')
        ->name('verify');
    Route::get('/{payment}/receipt', 'downloadReceipt')
        ->name('download-receipt');
});

// Payment Gateway Callback Routes
Route::prefix('payment-gateway')->name('payment.')->group(function () {
    Route::get('/callback', function () {
        return redirect()->route('payments.index')->with('success', __('Payment completed. Your payment is being processed.'));
    })->name('callback');

    Route::post('/webhook', function () {
        // This would normally be handled by a dedicated controller
        return response()->json(['status' => 'success']);
    })->name('webhook');
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

// Registration Page - This uses the Livewire component
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

require __DIR__.'/auth.php';
