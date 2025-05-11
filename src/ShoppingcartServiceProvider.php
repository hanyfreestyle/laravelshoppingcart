<?php

namespace FreestyleRepo\Shoppingcart;

use Illuminate\Auth\Events\Logout;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;

class ShoppingcartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Binding the cart service
        $this->app->bind('cart', Cart::class);

        // Merge the config
        $this->mergeConfigFrom(__DIR__ . '/../config/cart.php', 'cart');
    }

    public function boot(): void
    {
        // Publish the config
        $this->publishes([
            __DIR__ . '/../config/cart.php' => config_path('cart.php')
        ], 'config');

        // Destroy cart on logout
        $this->app['events']->listen(Logout::class, function () {
            if (config('cart.destroy_on_logout')) {
                app(SessionManager::class)->forget('cart');
            }
        });

        // Publish migration if class doesn't exist
        if (!class_exists('CreateShoppingcartTable')) {
            $timestamp = date('Y_m_d_His');
            $this->publishes([
                __DIR__ . '/../database/migrations/0000_00_00_000000_create_shopping_cart_table.php'
                => database_path('migrations/' . $timestamp . '_create_shoppingcart_table.php'),
            ], 'migrations');
        }
    }
}