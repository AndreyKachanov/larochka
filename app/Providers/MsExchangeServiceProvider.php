<?php

namespace App\Providers;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use jamesiarmes\PhpEws\Client;

class MsExchangeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function (Application $app) {
            $config = $app->make('config')->get('mail');
            return new Client(
                $config['ms_exchange_server'], $config['ms_exchange_login'], $config['ms_exchange_password'], Client::VERSION_2010
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
