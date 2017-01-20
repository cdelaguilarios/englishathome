<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Mensajes;
use Validator;
use App\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller {

    use AuthenticatesAndRegistersUsers,
        ThrottlesLogins;

    protected $loginPath = "";
    protected $redirectTo = "/";

    public function __construct() {
        $this->middleware($this->guestMiddleware(), ["except" => "getLogout"]);
        $this->loginPath = route("auth.login");
    }

    public function authenticated($request, $user) {
        if (!Usuario::usuarioEliminado($user["idEntidad"])) {
            Auth::logout();
            Mensajes::agregarMensajeAdvertencia("Su cuenta ha sido eliminada.");
        } else if (!Usuario::usuarioActivo($user["idEntidad"])) {
            Auth::logout();
            Mensajes::agregarMensajeAdvertencia("Su cuenta ha sido desactivada.");
        }
        return redirect()->intended($this->redirectPath());
    }

    protected function validator(array $data) {
        return Validator::make($data, [
                    "email" => "required|email|max:255|unique:" . Usuario::nombreTabla(),
                    "password" => "required|min:6|confirmed",
        ]);
    }

    protected function create(array $data) {
        $usuario = new Usuario([
            "email" => $data["email"],
            "password" => bcrypt($data["password"]),
        ]);
        $usuario->save();
        return $usuario;
    }

}
