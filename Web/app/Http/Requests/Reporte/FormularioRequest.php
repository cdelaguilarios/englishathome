<?php

namespace App\Http\Requests\Reporte;

use App\Models\Reporte;
use App\Models\TipoDocumento;
use App\Http\Requests\Request;
use App\Helpers\ReglasValidacion;
use App\Helpers\Enum\SexosEntidad;
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
            $camposValidos = $this->validarFiltro($datos, $reglasValidacion, $datosEntidad->nombre, $campoSel, $listaCamposEntidad[$campoSel]);
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
              $camposValidos = $this->validarFiltro($datos, $reglasValidacion, $datosEntidadRelacionada->nombre, $campoSel, $listaCamposEntidadRelacionada[$campoSel]);
            }
            if (!$camposValidos) {
              break;
            }
          }
          if (!($camposValidos && $this->validarFiltro($datos, $reglasValidacion, $datosEntidadRelacionada->nombre, "busqueda", ["tipo" => "busqueda"]))) {
            break;
          }
        }
      }
    }

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

  public function messages() {
    return [
        "tipoEntidadNoValido.required" => "Tipo de entidad seleccionada no válida.",
        "camposEntidadNoValidos.required" => "Se debe seleccionar por lo menos un campo para la entidad.",
        "campoEntidadNoValido.required" => "Uno o más campos de la entidad no son válidos.",
        "tipoEntidadRelacionadaNoValido.required" => "Uno o más tipos de entidades relacionadas no son válidos.",
        "camposEntidadRelacionadaNoValidos.required" => "Se debe seleccionar por lo menos un campo por cada entidad relacionada.",
        "campoEntidadRelacionadaNoValido.required" => "Uno o más campos de las entidades relacionadas no son válidos.",
        "campoNoValido.required" => "Uno o más campos no son válidos."
    ];
  }

  private function validarFiltro(&$datos, &$reglasValidacion, $entidad, $campo, $datosCampo) {
    $nomTipoFiltro = strtolower("sel-tipo-filtro-" . $entidad . "-" . $campo);
    $nomFiltro = strtolower("inp-filtro-" . $entidad . "-" . $campo);

    $datTipoFiltroValido = (isset($datos[$nomTipoFiltro]) && !empty($datos[$nomTipoFiltro]));
    $datFiltroValido = (isset($datos[$nomFiltro]) && !empty($datos[$nomFiltro]));
    if ((!$datTipoFiltroValido && !in_array(strtolower($datosCampo["tipo"]), ["tinyint", "sexo", "tipodocumento"])) ||
            (!$datFiltroValido && !in_array(strtolower($datosCampo["tipo"]), ["datetime", "timestamp", "tinyint"]))) {
      return TRUE;
    }

    $campoValido = TRUE;
    switch (strtolower($datosCampo["tipo"])) {
      case "varchar":
      case "text":
      case "char":
        $campoValido = in_array($datos[$nomTipoFiltro], ["=", "<>", "LIKE", "NOT LIKE"]);
        if ($campoValido) {
          $reglasValidacion[$nomFiltro] = "max:255";
        }
        break;
      case "int":
      case "float":
        $campoValido = in_array($datos[$nomTipoFiltro], ["=", "<>", "LIKE", "NOT LIKE", ">", ">=", "<", "<="]);
        if ($campoValido) {
          $reglasValidacion[$nomFiltro] = ["regex:" . ReglasValidacion::RegexDecimalNegativo];
        }
        break;
      case "datetime":
      case "timestamp":
        $campoValido = in_array($datos[$nomTipoFiltro], ["=", "<>", ">", ">=", "<", "<=", "BETWEEN"]);
        $nomFechaIniFiltro = strtolower("inp-filtro-fecha-inicio-" . $entidad . "-" . $campo);
        $nomFechaFinFiltro = strtolower("inp-filtro-fecha-fin-" . $entidad . "-" . $campo);
        $datFiltroFechaIniValido = (isset($datos[$nomFechaIniFiltro]) && !empty($datos[$nomFechaIniFiltro]));
        $datFiltroFechaFinValido = (isset($datos[$nomFechaFinFiltro]) && !empty($datos[$nomFechaFinFiltro]));

        if ($campoValido && $datFiltroFechaIniValido) {
          $reglasValidacion[$nomFechaIniFiltro] = ["regex:" . ReglasValidacion::RegexFecha];
        }
        if ($campoValido && $datFiltroFechaIniValido && $datFiltroFechaFinValido && $datos[$nomTipoFiltro] == "BETWEEN") {
          $reglasValidacion[$nomFechaFinFiltro] = ["regex:" . ReglasValidacion::RegexFecha];
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
