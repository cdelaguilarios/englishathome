<?php

namespace App\Http\ViewComposers;

use Auth;
use Config;
use App\Models\Curso;
use App\Models\Usuario;
use App\Helpers\Enum\RolesUsuario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class ProfileComposer {

  /**
   * The user repository implementation.
   *
   * @var UserRepository
   */
  protected $users;

  /**
   * Create a new profile composer.
   *
   * @param  UserRepository  $users
   * @return void
   */
  public function __construct() {
    
  }

  /**
   * Bind data to the view.
   *
   * @param  View  $view
   * @return void
   */
  public function compose(View $view) {
    $datosExtrasVistas = Cache::get("datosExtrasVistas");
    if (!isset($datosExtrasVistas)) {
      $datosExtrasVistas = [
          "urlSitioWebEmpresa" => Config::get("eah.urlSitioWebEmpresa"),
          "nombreComercialEmpresa" => Config::get("eah.nombreComercialEmpresa"),
          "cursos" => Curso::listarSimple(),
          "minHorasClase" => Config::get("eah.minHorasClase"),
          "maxHorasClase" => Config::get("eah.maxHorasClase"),
          "minHorario" => Config::get("eah.minHorario"),
          "maxHorario" => Config::get("eah.maxHorario"),
          "rolesUsuarios" => RolesUsuario::listar(),
          "rolesUsuariosSistema" => RolesUsuario::listarDelSistema(),
      ];
      Cache::put("datosExtrasVistas", $datosExtrasVistas, 120);
    }
    foreach ($datosExtrasVistas as $k => $v) {
      $view->with($k, $v);
    }
    if (!(Auth::guest())) {
      $view->with("usuarioActual", Usuario::obtenerActual());
    }
  }

}
