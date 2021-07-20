<?php

namespace Agenciafmd\Infomais\Providers;

use Illuminate\Support\ServiceProvider;

class InfomaisServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 
    }

    public function register()
    {
        $this->loadConfigs();
        $this->publish();
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-infomais.php' => base_path('config/laravel-infomais.php'),
        ], 'laravel-infomais:configs');

    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-infomais.php', 'laravel-infomais');
    }
}
