<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Member\DependentController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\NotificationPreferenceController;
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

// Language switch route
Route::get('/language/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ms'])) {
        abort(400);
    }

    session()->put('language', $locale);

    if (\Illuminate\Support\Facades\Auth::check()) {
        \Illuminate\Support\Facades\Auth::user()->update(['language' => $locale]);
    }

    return redirect()->back();
})->name('language.switch');

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

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/members', [AdminController::class, 'members'])->name('members');
    Route::get('/members/{member}', [AdminController::class, 'memberDetails'])->name('members.show');

    // System Settings Routes
    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/{key}', [SystemSettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/bulk-update', [SystemSettingsController::class, 'bulkUpdate'])->name('settings.bulk-update');

    // Reports Routes
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/payment-summary', [App\Http\Controllers\Admin\ReportController::class, 'paymentSummary'])->name('reports.payment-summary');
    Route::get('/reports/member-statistics', [App\Http\Controllers\Admin\ReportController::class, 'memberStatistics'])->name('reports.member-statistics');
    Route::get('/reports/payment-history', [App\Http\Controllers\Admin\ReportController::class, 'paymentHistory'])->name('reports.payment-history');

    Route::get('/payment-verification', function () {
        return view('admin.payment-verification');
    })->name('payment-verification');
});

// Notification Routes
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/preferences', [NotificationPreferenceController::class, 'index'])->name('preferences');
    Route::get('/history', [NotificationPreferenceController::class, 'history'])->name('history');
    Route::post('/mark-read/{id}', [NotificationPreferenceController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationPreferenceController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/delete/{id}', [NotificationPreferenceController::class, 'delete'])->name('delete');
    Route::delete('/delete-all', [NotificationPreferenceController::class, 'deleteAll'])->name('delete-all');
});

require __DIR__.'/auth.php';
