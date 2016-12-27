<?php

namespace App\Http\ViewComposers;

use Auth;
use Config;
use App\Models\Curso;
use App\Models\Usuario;
use App\Models\NivelIngles;
use App\Models\TipoDocumento;
use App\Helpers\Enum\MotivosPago;
use App\Helpers\Enum\EstadosPago;
use App\Helpers\Enum\EstadosClase;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\TiposEntidad;
use App\Helpers\Enum\EstadosAlumno;
use Illuminate\Contracts\View\View;
use App\Helpers\Enum\GenerosEntidad;
use App\Helpers\Enum\EstadosProfesor;
use App\Helpers\Enum\EstadosInteresado;
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
    if (!(Auth::guest())) {
      $view->with("usuarioActual", Usuario::obtenerXId(Auth::user()->idEntidad));
      $view->with("rolesUsuarios", RolesUsuario::listar());
      $view->with("tiposDocumentos", TipoDocumento::listarSimple());
      $view->with("nivelesIngles", NivelIngles::listarSimple());
      $view->with("estadosClase", EstadosClase::listar());
      $view->with("estadosClaseSimple", EstadosClase::listarSimple());   
      $view->with("estadoPagoRealizado", EstadosPago::Realizado);
      $view->with("estadosPago", EstadosPago::listar());
      $view->with("estadosPagoSimple", EstadosPago::listarSimple());
      $view->with("estadosInteresado", EstadosInteresado::listar());
      $view->with("estadosAlumno", EstadosAlumno::listar());
      $view->with("estadosProfesor", EstadosProfesor::listar());
      $view->with("estadoClaseRealizada", EstadosClase::Realizada);
      $view->with("estadoClaseCancelada", EstadosClase::Cancelada);
      $view->with("tipoCancelacionClaseAlumno", TiposCancelacionClase::CancelacionAlumno);
      $view->with("motivosPago", MotivosPago::listar());
      $view->with("tiposDocente", TiposEntidad::listarTiposDocente());
      $view->with("generos", GenerosEntidad::listar());
      $view->with("cursos", Curso::listarSimple());
      $view->with("minHorasClase", Config::get("eah.minHorasClase"));
      $view->with("maxHorasClase", Config::get("eah.maxHorasClase"));
      $view->with("minHorario", Config::get("eah.minHorario"));
      $view->with("maxHorario", Config::get("eah.maxHorario"));
    }
  }

}
