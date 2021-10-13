<?php

namespace Hostville\Modullo\UserLaravel;


use Hostville\Modullo\UserLaravel\Auth\ModulloUser;
use Hostville\Modullo\UserLaravel\Auth\ModulloUserProvider;
use Hostville\Modullo\Sdk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ServiceProvider;

class ModulloServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap required services
     */



    public function boot()
    {
        // publish the config file
        $this->publishes([
            __DIR__.'/config/modullo-api.php' => config_path('modullo-api.php'),
        ], 'modullo-setup');



        // check if the Sdk has already been added to the container
        if (!$this->app->has(Sdk::class)) {
            $userId = Cookie::get('store_id');



            /**
             * modullo SDK
             */
            $this->app->singleton(Sdk::class, function ($app) use ($userId) {
                $token = !empty($userId) ? Cache::get('modullo.auth_token.'.$userId, null) : null;
                # get the token from the cache, if available
                $config = $app->make('config');
                # get the configuration object
                $config = [
                    'credentials' => [
                        'id' => $config->get('modullo-api.client.id'),
                        'secret' => $config->get('modullo-api.client.secret'),
                        'token' => $token,
                        'environment' => $config->get('modullo-api.env'),
                    ]
                ];
                return new Sdk($config);
            });
        }
        // add the modullo API user provider
        $this->app->when(ModulloUser::class)
                    ->needs(Sdk::class)
                    ->give(function () {
                        return $this->app->make(Sdk::class);
                    });
        # provide the requirement
        Auth::provider('modullo', function ($app, array $config) {
            return new ModulloUserProvider($app->make(Sdk::class), $config);
        });
    }

    public function register()
    {
      $this->mergeConfigFrom(
        __DIR__.'/config/environments.php', 'modullo-api.environments'
      );
    }
}