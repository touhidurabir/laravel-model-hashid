<?php

namespace Touhidurabir\ModelHashid;

use Illuminate\Support\ServiceProvider;
use Touhidurabir\ModelHashid\Hasher\Hasher;

class ModelHashidServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        $this->publishes([
            __DIR__.'/../config/hasher.php' => base_path('config/hasher.php'),
        ], 'config');
    }
    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

        $this->mergeConfigFrom(
            __DIR__.'/../config/hasher.php', 'hasher'
        );

        $this->app->bind('model-hashid', function () {
            
            return new Hasher(
                config('hasher.key'), 
                config('hasher.padding'), 
                config('hasher.alphabets')
            );
        });
    }
    
}