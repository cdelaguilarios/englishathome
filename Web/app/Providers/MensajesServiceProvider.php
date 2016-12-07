<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class MensajesServiceProvider extends ServiceProvider {

    public function boot() {
        //
    }

    public function register() {        
        App::bind('mensajes', function()
        {
            return new \App\Libraries\Mensajes;
        });
    }

}
