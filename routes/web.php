<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPhoneNumberController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminTemplateController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\InboxController;
use App\Http\Controllers\Web\ConversationController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\Web\SetupWizardController;
use App\Http\Controllers\Web\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Language switcher
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Currency switcher
Route::get('/currency/{currency}', [LocaleController::class, 'switchCurrency'])->name('currency.switch');

// Pricing page
Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

// Terms of Service
Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');

// Privacy Policy
Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

// Checkout cancel (accessible without full auth for Stripe redirect)
Route::get('/subscription/checkout-cancelled', [SubscriptionController::class, 'cancel'])->name('subscription.checkout-cancelled');

// Subscription checkout flow (before setup - user just registered)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscription/checkout/{plan}/{interval?}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
});

// Setup Wizard (after payment, before setup completed)
Route::middleware(['auth', 'verified', 'subscribed'])->group(function () {
    Route::get('/setup', [SetupWizardController::class, 'index'])->name('setup.index');
    Route::get('/setup/welcome', [SetupWizardController::class, 'welcome'])->name('setup.welcome');
    Route::get('/setup/clinic', [SetupWizardController::class, 'clinic'])->name('setup.clinic');
    Route::post('/setup/clinic', [SetupWizardController::class, 'storeClinic'])->name('setup.store.clinic');
    Route::get('/setup/phone', [SetupWizardController::class, 'phone'])->name('setup.phone');
    Route::get('/setup/complete', [SetupWizardController::class, 'complete'])->name('setup.complete');
    Route::post('/setup/finish', [SetupWizardController::class, 'finish'])->name('setup.finish');
});

// Subscription management (after setup completed, accessible even without active subscription)
Route::middleware(['auth', 'verified', 'setup.completed'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('subscription.billing-portal');
    Route::get('/subscription/invoices', [SubscriptionController::class, 'invoices'])->name('subscription.invoices');
    Route::post('/subscription/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscription.change-plan');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resumeSubscription'])->name('subscription.resume');
});

// Main app routes (require setup completed AND active subscription)
Route::middleware(['auth', 'verified', 'setup.completed', 'subscribed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/unread-count', [InboxController::class, 'unreadCount'])->name('inbox.unread-count');

    Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages'])->name('conversations.messages');
    Route::post('/conversations/{id}/reply', [ConversationController::class, 'reply'])->name('conversations.reply');
    Route::post('/conversations/{id}/outcome', [ConversationController::class, 'outcome'])->name('conversations.outcome');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.update.general');
    Route::put('/settings/followup', [SettingsController::class, 'updateFollowup'])->name('settings.update.followup');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.update.notifications');
    Route::post('/settings/templates', [SettingsController::class, 'storeTemplate'])->name('settings.store.template');
    Route::put('/settings/templates/{templateId}', [SettingsController::class, 'updateTemplate'])->name('settings.update.template');
    Route::patch('/settings/templates/{templateId}/toggle', [SettingsController::class, 'toggleTemplate'])->name('settings.toggle.template');
    Route::delete('/settings/templates/{templateId}', [SettingsController::class, 'destroyTemplate'])->name('settings.destroy.template');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Phone Numbers Management
    Route::get('/phone-numbers', [AdminPhoneNumberController::class, 'index'])->name('phone-numbers.index');
    Route::post('/phone-numbers', [AdminPhoneNumberController::class, 'store'])->name('phone-numbers.store');
    Route::put('/phone-numbers/{id}', [AdminPhoneNumberController::class, 'update'])->name('phone-numbers.update');
    Route::patch('/phone-numbers/{id}/toggle', [AdminPhoneNumberController::class, 'toggle'])->name('phone-numbers.toggle');
    Route::delete('/phone-numbers/{id}', [AdminPhoneNumberController::class, 'destroy'])->name('phone-numbers.destroy');

    // Templates Management
    Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
    Route::put('/templates/{id}', [AdminTemplateController::class, 'update'])->name('templates.update');

    // Plans & Prices
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
    Route::put('/plans/{id}', [AdminPlanController::class, 'update'])->name('plans.update');
    Route::patch('/plans/{id}/toggle-active', [AdminPlanController::class, 'toggleActive'])->name('plans.toggle-active');
    Route::patch('/plans/{id}/toggle-featured', [AdminPlanController::class, 'toggleFeatured'])->name('plans.toggle-featured');
    Route::post('/plan-prices', [AdminPlanController::class, 'storePrice'])->name('plan-prices.store');
    Route::put('/plan-prices/{id}', [AdminPlanController::class, 'updatePrice'])->name('plan-prices.update');

    // Subscriptions & Transactions
    Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/transactions', [AdminSubscriptionController::class, 'transactions'])->name('subscriptions.transactions');
});

require __DIR__.'/auth.php';
