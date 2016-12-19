<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Mensajes extends Facade {

  protected static function getFacadeAccessor() {
    return "mensajes";
  }

}
