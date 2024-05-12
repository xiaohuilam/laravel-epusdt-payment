<?php

namespace Xiaohuilam\LaravelEpusdtPayment\Providers;

use Illuminate\Support\ServiceProvider;
use Xiaohuilam\LaravelEpusdtPayment\Epusdt;

class LaravelEpusdtPaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'epusdt');

        $this->app->singleton('epusdt', function () {
            return new Epusdt(config('epusdt.url'), config('epusdt.token'));
        });
    }
}
