<?php

namespace App\Http\Requests\Configuracion;

use App\Http\Requests\Request;
use App\Models\VariableSistema;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposVariableConfiguracion;

class FormularioRequest extends Request/* - */ {

  public function authorize()/* - */ {
    return true;
  }

  protected function getValidatorInstance()/* - */ {
    $datos = $this->all();
    $variablesSistema = VariableSistema::listar();
    foreach ($variablesSistema as $variableSistema) {
      $datos[$variableSistema->llave] = ReglasValidacion::formatoDato($datos, $variableSistema->llave);
    }
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules()/* - */ {
    $datos = $this->all();

    $reglasValidacion = [];
    $variablesSistema = VariableSistema::listar();
    foreach ($variablesSistema as $variableSistema) {
      switch ($variableSistema->tipo) {
        case TiposVariableConfiguracion::Password: {
            if ($datos[$variableSistema->llave] != NULL && $datos[$variableSistema->llave] != "") {
              $reglasValidacion[$variableSistema->llave] = "required|confirmed|max:255";
            }
            break;
          }
        case TiposVariableConfiguracion::Correo: {
            if ($datos[$variableSistema->llave] != NULL && $datos[$variableSistema->llave] != "") {
              $reglasValidacion[$variableSistema->llave] = "required|email|max:255";
            }
            break;
          }
        case TiposVariableConfiguracion::Texto: {
            if ($datos[$variableSistema->llave] != NULL && $datos[$variableSistema->llave] != "") {
              $reglasValidacion[$variableSistema->llave] = "required|max:255";
            }
            break;
          }
        case TiposVariableConfiguracion::TextoArea:
        case TiposVariableConfiguracion::TextoAreaEditor: {
            if ($datos[$variableSistema->llave] != NULL && $datos[$variableSistema->llave] != "") {
              $reglasValidacion[$variableSistema->llave] = "required|max:8000";
            }
            break;
          }
        default:break;
      }
    }

    switch ($this->method()) {
      case "GET":
      case "DELETE":
      case "PUT":
      case "PATCH": {
          return [];
        }
      case "POST": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

}
