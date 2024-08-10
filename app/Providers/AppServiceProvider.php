<?php

namespace App\Providers;

use App\Models\Wishlist;
use App\Services\ApService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApService::class, function ($app) {
            return new ApService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            if (auth()->check()) {
                $wishlistItems = Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray();
                $view->with('wishlistItems', $wishlistItems);
            }
        });

    }
}
