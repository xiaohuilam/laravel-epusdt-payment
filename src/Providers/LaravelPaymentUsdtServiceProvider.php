<?php

namespace Xiaohuilam\LaravelPaymentUsdt\Providers;

use Illuminate\Support\ServiceProvider;
use Xiaohuilam\LaravelPaymentUsdt\Epusdt;

class LaravelPaymentUsdtServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'epusdt');

        $this->app->singleton('epusdt', function () {
            return new Epusdt(config('epusdt.url'), config('epusdt.token'));
        });
    }
}
