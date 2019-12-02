<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Enum\TiposEntidad;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model {

  public $timestamps = false;
  protected $table = "curso";
  protected $fillable = ["nombre", "descripcion", "modulos", "metodologia", "incluye", "inversion", "incluirInversionCuotas", "inversionCuotas", "notasAdicionales", "adjuntos", "activo"];

  public static function nombreTabla()/* - */ {
    $modeloCurso = new Curso();
    $nombreTabla = $modeloCurso->getTable();
    unset($modeloCurso);
    return $nombreTabla;
  }

  public static function listar()/* - */ {
    return Curso::where("eliminado", 0);
  }

  public static function listarBusqueda($terminoBus = NULL)/* - */ {
    $cursos = Curso::listar()->select("id", "nombre");
    if (isset($terminoBus)) {
      $cursos->whereRaw('nombre like ?', ["%{$terminoBus}%"]);
    }    
    return $cursos->lists("nombre", "id");
  }

  public static function listarSimple($soloActivos = TRUE)/* - */ {
    $cursos = Curso::listar();
    if ($soloActivos) {
      $cursos->where("activo", 1);
    }
    return $cursos->lists("nombre", "id");
  }

  public static function obtenerXId($id)/* - */ {
    return Curso::listar()->where("id", $id)->firstOrFail();
  }

  public static function registrar($req)/* - */ {
    $datos = $req->all();
    $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO("", $datos, 20, "Adjuntos");
    
    $curso = new Curso($datos);
    $curso->fechaRegistro = Carbon::now()->toDateTimeString();
    $curso->save();
    
    $imagen = $req->file("imagen");
    if (isset($imagen) && $imagen != "") {
      $curso->imagen = Archivo::registrar($curso->id . "_ic_", $imagen);
      $curso->save();
    }
    
    Cache::forget("datosExtrasVistas");
    return $curso->id;
  }

  public static function actualizar($id, $req)/* - */ {
    $curso = Curso::obtenerXId($id);
    
    $datos = $req->all(); 
    $datos["adjuntos"] = Archivo::procesarArchivosSubidosNUEVO($curso->adjuntos, $datos, 20, "Adjuntos");    
    $curso->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $curso->update($datos);
    
    $imagen = $req->file("imagen");
    if (isset($imagen) && $imagen != "") {
      if (isset($curso->imagen) && $curso->imagen != "") {
        Archivo::eliminar($curso->imagen);
      }
      $curso->imagen = Archivo::registrar($curso->id . "_ic_", $imagen);
      $curso->save();
    }
    Cache::forget("datosExtrasVistas");
  }

  public static function eliminar($id)/* - */ {
    $curso = Curso::obtenerXId($id);
    $curso->eliminado = 1;
    $curso->fechaUltimaActualizacion = Carbon::now()->toDateTimeString();
    $curso->save();
  }

  //REPORTE
  public static function listarCampos() {
    return [
        "nombre" => ["titulo" => "Nombre"],
        "descripcion" => ["titulo" => "Descripción"],
        "modulos" => ["titulo" => "Modulos"],
        "metodologia" => ["titulo" => "Metodología"],
        "incluye" => ["titulo" => "Incluye"],
        "inversion" => ["titulo" => "Inversion"],
        "inversionCuotas" => ["titulo" => "Inversion en cuotas"],
        "notasAdicionales" => ["titulo" => "Notas adicionales"],
        "activo" => ["titulo" => "Activo"]
    ];
  }

  public static function listarEntidadesRelacionadas() {
    return [TiposEntidad::Interesado, TiposEntidad::Alumno, TiposEntidad::Postulante, TiposEntidad::Profesor];
  }

  public static function obtenerConsultaBdReporte($campos, $filtros = NULL, $entidadRelacionada = NULL, $idEntidadRelacionada = NULL, $camposEntidadRelacionada = NULL) {
    $camposConsultaBd = $campos;
    $tablasConsultaBD = "FROM " . Curso::nombreTabla();
    $filtrosConsultaBD = [];

    if (!is_null($entidadRelacionada)) {
      $tablasConsultaBD .= " LEFT JOIN " . EntidadCurso::nombreTabla() . " ON " . EntidadCurso::nombreTabla() . ".idCurso = " . Curso::nombreTabla() . ".id";
      $tablasConsultaBD .= " LEFT JOIN " . Entidad::nombreTabla() . " ON " . Entidad::nombreTabla() . ".id = " . EntidadCurso::nombreTabla() . ".idEntidad";
      $filtrosConsultaBD += [
          "campo" => Entidad::nombreTabla() . ".tipo",
          "operador" => "=",
          "valor" => $entidadRelacionada
      ];
      if (!is_null($idEntidadRelacionada) && !is_null($camposEntidadRelacionada)) {
        $camposConsultaBd += $camposEntidadRelacionada;
        $filtrosConsultaBD += [
            "campo" => Entidad::nombreTabla() . ".id",
            "operador" => "=",
            "valor" => $idEntidadRelacionada
        ];
      } else {
        $camposConsultaBd += ["COUNT(" . Entidad::nombreTabla() . ".id) AS total" . ucfirst(strtolower($entidadRelacionada))];
      }
    }
    if (!is_null($filtros)) {
      $filtrosConsultaBD += $filtros;
    }
    return Reporte::generarConsultaBd($camposConsultaBd, $tablasConsultaBD, $filtrosConsultaBD);
  }

}
