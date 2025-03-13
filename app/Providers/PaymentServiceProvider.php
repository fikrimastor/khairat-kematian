<?php

namespace App\Providers;

use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the payment gateway factory
        $this->app->singleton(PaymentGatewayFactory::class, function ($app) {
            return new PaymentGatewayFactory;
        });

        // Register payment action bindings
        $this->registerPaymentActions();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register payment action bindings.
     */
    protected function registerPaymentActions(): void
    {
        // No bindings needed as we're using Action classes directly
        // This method is here for future extensibility
    }
}
