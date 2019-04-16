@extends("layouts.master")
@section("titulo", "Ficha " . ($alumno->sexo == "F" ? "de la alumna" : "del alumno") . " " . $alumno->nombre . " " .  $alumno->apellido)

@section("tituloImpresion")
<b>Ficha {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }}<br/>{{ $alumno->nombre . " " .  $alumno->apellido }}<br/><small><i class="fa fa-fw fa-envelope"></i> {{ $alumno->correoElectronico }}<br/><i class="fa fa-fw fa-phone"></i> {{ $alumno->telefono }}</small></b>
@endsection

@section("section_script")
@if(isset($impresionDirecta) && $impresionDirecta)
<script>
  window.onafterprint = function (e) {
    $(window).off('mousemove', window.onafterprint);
    window.close();
  };

  const $patchedStyle = $('<style media="print">')
  .text(`
    img { max-width: none !important; }
    a[href]:after { content: ""; }
  `)
  .appendTo('head');
  window.onload = function() { 
    window.print(); 
    $(window).one('mousemove', window.onafterprint);
  }
</script>
@endif
@endsection

@section("section_style")
<style>
  body{
    font-size: 14px;
  }
  .titulo{
    text-align: center;
  }
  .profile-user-img{
    padding: 0;
    border: 0;
    float: right;
  }
  .login-logo, .register-logo{
    font-size: 30px;
  }
  .sec-ficha-nombre-entidad small{
    font-size: 70%;
    line-height: 1.2;
    display: block;
    padding-top: 10px;
  }
  hr {
    margin-top: 2px;
    margin-bottom: 2px;
    border: 0;
    border-top: 1px solid #fff;
  }
  .sec-mapa {
    height: 100%;
    margin-left: 20px;
  }
  @media print {
    html, body {
      width: 210mm;
      height: 297mm;        
    }
    .pagina-impresion {
      margin: 0;
      page-break-after: always;
    }
  }
</style>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-offset-1 col-sm-10">
    <div class="sec-datos">
      <div class="box-body">
        <div class="pagina-impresion">
          <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
          <p class="text-muted">
            @include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar"])
            @include("util.imagenPerfil", ["entidad" => $alumno])
          </p>
          <hr>   
          @if(isset($alumno->idCurso))
          <strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso</strong>
          <p class="text-muted">{!! App\Models\Curso::listarSimple(FALSE)[$alumno->idCurso] !!}</p>
          <hr> 
          @endif 
          @if(isset($alumno->profesorProximaClase)) 
          <strong><i class="fa fa-fw flaticon-teach"></i> Profesor</strong>
          <p class="text-muted">{{ $alumno->profesorProximaClase->nombre . " " .  $alumno->profesorProximaClase->apellido }}<br>(Pago por hora de clase: <b>{{ number_format($alumno->datosProximaClase->costoHoraProfesor, 2, ".", ",") }}</b>)</p>
          <hr> 
          @endif
          @if(isset($alumno->datosProximaClase) && isset($alumno->datosProximaClase->tiempos))
          <strong><i class="fa fa-fw fa-clock-o"></i> Total de horas pagadas</strong>
          <p class="text-muted">{{ App\Helpers\Util::formatoHora($alumno->datosProximaClase->tiempos->duracionTotal) }}</p>
          <hr> 
          @endif
          <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
          <p class="text-muted">{{ $alumno->direccion }}{!! ((isset($alumno->numeroDepartamento) && $alumno->numeroDepartamento != "") ? "<br/>Depto./Int " . $alumno->numeroDepartamento : "") !!}{!! ((isset($alumno->referenciaDireccion) && $alumno->referenciaDireccion != "") ? " - " . $alumno->referenciaDireccion : "") !!}<br/>{{ $alumno->direccionUbicacion }}</p>
          <div class="sec-mapa">
            <img id="img-mapa" src="http://maps.google.com/maps/api/staticmap?sensor=false&center={{ $alumno->geoLatitud }},{{ $alumno->geoLongitud }}&zoom=17&size=800x550&markers=color:0x3C8DBC|{{ $alumno->geoLatitud }},{{ $alumno->geoLongitud }}&key={{ Config::get("eah.apiKeyGoogleMaps") }}" />
          </div>
          <hr>
        </div>   
        <div>
          @if(isset($alumno->idNivelIngles) && $alumno->idNivelIngles !== "")
          <strong><i class="fa fa-fw fa-list-ol"></i> Nivel de ingles</strong>
          <p class="text-muted">
            {{ App\Models\NivelIngles::listarSimple()[$alumno->idNivelIngles] }}
          </p>
          <hr> 
          @endif
          @if(isset($alumno->inglesLugarEstudio) && trim($alumno->inglesLugarEstudio) != "")
          <strong><i class="fa fa-fw fa-institution"></i> Lugar donde estudio anteriormente</strong>
          <p class="text-muted">
            {{ $alumno->inglesLugarEstudio }}
          </p>
          <hr>  
          @endif
          @if(isset($alumno->inglesPracticaComo) && trim($alumno->inglesPracticaComo) != "")
          <strong><i class="fa fa-fw fa-commenting-o"></i> ¿Cómo practica?</strong>
          <p class="text-muted">
            {{ $alumno->inglesPracticaComo }}
          </p>
          <hr>  
          @endif
          @if(isset($alumno->inglesObjetivo) && trim($alumno->inglesObjetivo) != "")
          <strong><i class="fa fa-fw fa-check-square-o"></i> Objetivos específicos</strong>
          <p class="text-muted">
            {{ $alumno->inglesObjetivo }}
          </p>
          <hr>  
          @endif
          @if(isset($alumno->comentarioAdicional) && trim($alumno->comentarioAdicional) != "")
          <strong><i class="fa fa-fw fa-file-text"></i> Comentarios adicionales del alumno</strong>
          <p class="text-muted">
            {{ $alumno->comentarioAdicional }}
          </p>
          <hr>  
          @endif
          @if(isset($alumno->comentarioAdministrador) && trim($alumno->comentarioAdministrador) != "")
          <strong><i class="fa fa-fw fa-file-text"></i> Comentarios del administrador</strong>
          <p class="text-muted">
            {!! $alumno->comentarioAdministrador !!}
          </p>
          <hr>  
          @endif
        </div>  
      </div>
    </div>
  </div>
</div>
@endsection