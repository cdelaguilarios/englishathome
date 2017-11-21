<?php

namespace App\Http\Requests\Reporte;

use App\Models\Reporte;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\TiposEntidad;

class FormularioRequest extends Request {

  public function authorize() {
    return true;
  }

  protected function getValidatorInstance() {
    $datos = $this->all();
    $datos["id"] = ReglasValidacion::formatoDato($datos, "id", 0);
    $datos["titulo"] = ReglasValidacion::formatoDato($datos, "titulo");
    $datos["descripcion"] = ReglasValidacion::formatoDato($datos, "descripcion");
    $datos["entidad"] = ReglasValidacion::formatoDato($datos, "entidad");
    $datos["entidadesRelacionadas"] = ReglasValidacion::formatoDato($datos, "entidadesRelacionadas");
    $this->getInputSource()->replace($datos);
    return parent::getValidatorInstance();
  }

  public function rules() {
    $datos = $this->all();
    $reglasValidacion = [
        "titulo" => ["required", "max:100"],
        "descripcion" => "required|max:8000",
        "entidad" => "required"
    ];

    //Validaciones-Entidad
    $entidadValida = !is_null($datos["entidad"]);
    if ($entidadValida) {
      $datosEntidad = json_decode($datos["entidad"], false, 512, JSON_UNESCAPED_UNICODE);
      $listaTiposEntidad = TiposEntidad::listar();
      if (!(isset($datosEntidad->nombre) && array_key_exists($datosEntidad->nombre, $listaTiposEntidad))) {
        $reglasValidacion["tipoEntidadNoValido"] = "required";
        $entidadValida = FALSE;
      } else if (!(isset($datosEntidad->camposSel) && is_array($datosEntidad->camposSel))) {
        $reglasValidacion["camposEntidadNoValidos"] = "required";
        $entidadValida = FALSE;
      } else {
        $listaCamposEntidad = Reporte::listarCampos($datosEntidad->nombre);
        $camposValidos = TRUE;
        foreach ($datosEntidad->camposSel as $campoSel) {
          if (!array_key_exists($campoSel, $listaCamposEntidad)) {
            $reglasValidacion["campoEntidadNoValido"] = "required";
            $camposValidos = FALSE;
          } else {
            $camposValidos = $this->validarCampo($datos, $reglasValidacion, $datosEntidad->nombre, $listaCamposEntidad[$campoSel]);
          }
          if (!$camposValidos) {
            $entidadValida = FALSE;
            break;
          }
        }
      }
    }

    //Validaciones-Entidades Relacionadas
    if ($entidadValida && !is_null($datos["entidadesRelacionadas"])) {
      $entidadesRelacionadas = json_decode($datos["entidadesRelacionadas"], false, 512, JSON_UNESCAPED_UNICODE);
      if (!is_array($entidadesRelacionadas)) {
        $reglasValidacion["EntidadesRelacionadasNoValidas"] = "required";
        $entidadesRelacionadas = [];
      }
      $listaTiposEntidadesRelacionadas = Reporte::listarEntidadesRelacionadas($datosEntidad->nombre);
      foreach ($entidadesRelacionadas as $datosEntidadRelacionada) {
        if (!(isset($datosEntidadRelacionada->nombre) && array_key_exists($datosEntidadRelacionada->nombre, $listaTiposEntidadesRelacionadas))) {
          $reglasValidacion["tipoEntidadRelacionadaNoValido"] = "required";
          break;
        } else if (!(isset($datosEntidadRelacionada->camposSel) && is_array($datosEntidadRelacionada->camposSel))) {
          $reglasValidacion["camposEntidadRelacionadaNoValidos"] = "required";
          break;
        } else {
          $camposValidos = TRUE;
          $listaCamposEntidadRelacionada = Reporte::listarCampos($datosEntidadRelacionada->nombre);
          foreach ($datosEntidadRelacionada->camposSel as $campoSel) {
            if (!array_key_exists($campoSel, $listaCamposEntidadRelacionada)) {
              $reglasValidacion["campoEntidadRelacionadaNoValido"] = "required";
              $camposValidos = FALSE;
            } else {
              $camposValidos = $this->validarCampo($datos, $reglasValidacion, $datosEntidadRelacionada->nombre, $listaCamposEntidadRelacionada[$campoSel]);
            }
            if (!$camposValidos) {
              break;
            }
          }
          if (!($camposValidos && $this->validarCampo($datos, $reglasValidacion, $datosEntidadRelacionada->nombre, ["titulo" => "busqueda", "tipo" => "busqueda"]))) {
            break;
          }
        }
      }
    }
    print_r($reglasValidacion);
    die;

    switch ($this->method()) {
      case "GET":
      case "DELETE": {
          return [];
        }
      case "POST":
      case "PUT":
      case "PATCH": {
          return $reglasValidacion;
        }
      default:break;
    }
  }

  private function validarCampo(&$datos, &$reglasValidacion, $entidad, $campo) {
    $nomTipoFiltro = strtolower("sel-tipo-filtro-" . $entidad . "-" . $campo["titulo"]);
    $nomFiltro = strtolower("inp-filtro-" . $entidad . "-" . $campo["titulo"]);
    $campoValido = TRUE;

    if (!(isset($datos[$nomTipoFiltro]) && isset($datos[$nomFiltro])) && $campo["tipo"] != "tinyint") {
      return;
    }
    switch ($campo["tipo"]) {
      case "varchar":
      case "text":
      case "char":
        $campoValido = in_array($datos[$nomFiltro], ["=", "<>", "LIKE", "NOT LIKE"]);
        if ($campoValido) {
          $reglasValidacion[$datos[$nomFiltro]] = "max:255";
        }
        break;
      case "int":
      case "float":
        $campoValido = in_array($datos[$nomFiltro], ["=", "<>", "LIKE", "NOT LIKE", ">", ">=", "<", "<="]);
        if ($campoValido) {
          $reglasValidacion[$datos[$nomFiltro]] = ["regex:" . ReglasValidacion::RegexDecimalNegativo];
        }
        break;
      case "datetime":
      case "timestamp":
        $campoValido = in_array($datos[$nomFiltro], ["=", "<>", ">", ">=", "<", "<=", "BETWEEN"]);
        $nomFechaIniFiltro = strtolower("inp-filtro-fecha-inicio-" . $entidad . "-" . $campo["titulo"]);
        $nomFechaFinFiltro = strtolower("inp-filtro-fecha-fin-" . $entidad . "-" . $campo["titulo"]);

        if ($campoValido && isset($datos[$nomFechaIniFiltro])) {
          $reglasValidacion[$datos[$nomFechaIniFiltro]] = ["regex:" . ReglasValidacion::RegexFecha];
        }
        if ($campoValido && isset($datos[$nomFechaFinFiltro]) && $datos[$nomTipoFiltro] == "BETWEEN") {
          $reglasValidacion[$datos[$nomFechaFinFiltro]] = ["regex:" . ReglasValidacion::RegexFecha];
        }
        break;
      case "tinyint":
        $datos[$nomFiltro] = (isset($datos[$nomFiltro]) ? 1 : 0);
        break;
      case "sexo":
        $listaSexos = SexosEntidad::listar();
        $campoValido = array_key_exists($datos[$nomFiltro], $listaSexos);
        break;
      case "tipodocumento":
        $listaTiposDocumentos = TipoDocumento::listarSimple();
        $campoValido = array_key_exists($datos[$nomFiltro], $listaTiposDocumentos->toArray());
        break;
      case "busqueda":
        $campoValido = Reporte::validarEntidadRelacionada($entidad, $datos[$nomFiltro]);
        break;
    }
    if (!$campoValido) {
      $reglasValidacion["campoNoValido"] = "required";
    }
    return $campoValido;
  }

}
