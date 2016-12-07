<?php

namespace App\Http\ViewComposers;

use Auth;
use App\Models\Curso;
use App\Models\Usuario;
use App\Helpers\Enum\RolesUsuario;
use App\Helpers\Enum\TiposEntidad;
use Illuminate\Contracts\View\View;
use App\Helpers\Enum\GenerosEntidad;

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
            $view->with('usuarioActual', Usuario::obtenerXId(Auth::user()->idEntidad));
            $view->with('rolesUsuarios', RolesUsuario::Listar());
            $view->with('tiposDocente', TiposEntidad::listarTiposDocente());
            $view->with('generos', GenerosEntidad::listar());
            $view->with('cursos', Curso::listarSimple());
        }
    }

}
