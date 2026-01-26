<?php

namespace App\Providers;

use App\Models\Clinic;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Cashier to use Clinic as the billable model
        Cashier::useCustomerModel(Clinic::class);
        View::composer('layouts.navigation', function ($view) {
            $unreadCount = 0;

            if (Auth::check()) {
                $clinic = Auth::user()->clinics()->first();
                if ($clinic) {
                    $unreadCount = Conversation::forClinic($clinic->id)
                        ->where('is_active', true)
                        ->where(function ($query) {
                            $query->whereNull('last_staff_reply_at')
                                ->orWhereColumn('last_message_at', '>', 'last_staff_reply_at');
                        })
                        ->count();
                }
            }

            $view->with('unreadConversationsCount', $unreadCount);
        });
    }
}
