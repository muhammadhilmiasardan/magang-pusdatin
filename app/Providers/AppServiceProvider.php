<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Share notifications to the admin layout
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
                $globalNotifications = \App\Models\ActivityLog::with('user')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
            } else {
                $globalNotifications = collect([]);
            }
            $view->with('globalNotifications', $globalNotifications);
        });
    }
}
