<?php

namespace App\Http\ViewComposers;

use Auth;
use Config;
use App\Models\Curso;
use App\Models\Usuario;
use App\Models\NivelIngles;
use App\Models\TipoDocumento;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\SexosEntidad;
use Illuminate\Contracts\View\View;
use App\Helpers\Enum\EstadosProfesor;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Enum\TiposCancelacionClase;

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
    if (!\Illuminate\Support\Facades\Auth::check()) {
      return;
    }
    $datosExtrasVistas = Cache::get("datosExtrasVistas");
    if (!isset($datosExtrasVistas)) {
      $datosExtrasVistas = [
          "sexos" => SexosEntidad::listar(),
          "cursos" => Curso::listarSimple(),
          "nivelesIngles" => NivelIngles::listarSimple(),
          "tiposDocumentos" => TipoDocumento::listarSimple(),
          "minHorasClase" => Config::get("eah.minHorasClase"),
          "maxHorasClase" => Config::get("eah.maxHorasClase"),
          "minHorario" => Config::get("eah.minHorario"),
          "maxHorario" => Config::get("eah.maxHorario"),
          "estadosClase" => EstadosClase::listar(),
          "estadoClaseRealizada" => EstadosClase::Realizada,
          "estadoClaseCancelada" => EstadosClase::Cancelada,
          "tipoCancelacionClaseAlumno" => TiposCancelacionClase::CancelacionAlumno,
          "rolesUsuarios" => RolesUsuario::listar(),
          "estadosProfesor" => EstadosProfesor::listar(),
          "tiposDocente" => TiposEntidad::listarTiposDocente(),
          "estadoPagoRealizado" => EstadosPago::Realizado,
      ];
      Cache::put("datosExtrasVistas", $datosExtrasVistas, 1);
    }
    foreach ($datosExtrasVistas as $k => $v) {
      $view->with($k, $v);
    }
    if (!(Auth::guest())) {
      $view->with("usuarioActual", Usuario::obtenerActual());
    }
  }

}
