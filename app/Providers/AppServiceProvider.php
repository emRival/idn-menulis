<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
        // Force HTTPS in production (fixes ERR_TOO_MANY_REDIRECTS behind proxies)
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share navbar categories (cached for 10 minutes)
        View::composer('components.navbar', function ($view) {
            $navCategories = Cache::remember('nav_categories', 600, function () {
                return \App\Models\Category::withCount(['articles' => fn($q) => $q->where('status', 'published')])->get();
            });
            $view->with('navCategories', $navCategories);
            $view->with('registrationEnabled', \App\Models\Setting::registrationEnabled());
        });

        // Share footer categories (cached for 10 minutes)
        View::composer('components.footer', function ($view) {
            $footerCategories = Cache::remember('footer_categories', 600, function () {
                return \App\Models\Category::take(4)->get();
            });
            $view->with('footerCategories', $footerCategories);
        });
    }
}

