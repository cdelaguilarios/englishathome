@extends("layouts.master")
@section("titulo", "Ficha " . ($alumno->sexo == "F" ? "de la alumna" : "del alumno") . " " . $alumno->nombre . " " .  $alumno->apellido)

@section("section_script")
@if(isset($impresionDirecta) && $impresionDirecta)
<script>
  var verificarMapa = setInterval(function () {
    mapa = (typeof (mapa) === "undefined" ? false : mapa);
    if (mapa) {
      verificarPosicionSel();
      setTimeout(function () {
        $("div:contains('Map data'):last").html("");
        window.onafterprint = function (e) {
          $(window).off('mousemove', window.onafterprint);
          window.close();
        };
        window.print();
        setTimeout(function () {
          $(window).one('mousemove', window.onafterprint);
        }, 1000);
      }, 500);
      clearInterval(verificarMapa);
    }
  }, 100);
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
  hr {
    margin-top: 2px;
    margin-bottom: 2px;
    border: 0;
    border-top: 1px solid #fff;
  }
  .sec-mapa {
    height: 200px;
    margin-left: 20px;
  }
  a[href^="http://maps.google.com/maps"]{display:none !important}
  a[href^="https://maps.google.com/maps"]{display:none !important}
  .gmnoprint a, .gmnoprint span, .gm-style-cc {display:none;}
  .gmnoprint div {display:none !important;}
  .gm-style-cc {display:none;}
</style>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-offset-1 col-sm-10">
    <div class="sec-datos">
      <div class="box-body">
        <strong><i class="fa fa-fw fa-envelope"></i> Correo electrónico</strong>
        <p class="text-muted">
          {{ $alumno->correoElectronico }}
          @include("util.imagenPerfil", ["entidad" => $alumno])
        </p>
        <hr> 
        <strong><i class="fa fa-fw fa-calendar"></i> Horario</strong>
        <p class="text-muted">
          @include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar"])
        </p>
        <hr>   
        @if(isset($alumno->idCurso))
        <strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso</strong>
        <p class="text-muted">{{ $cursos[$alumno->idCurso] }}</p>
        <hr> 
        @endif
        <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
        <p class="text-muted">{{ $alumno->direccion }}{!! ((isset($alumno->numeroDepartamento) && $alumno->numeroDepartamento != "") ? "<br/>Depto./Int " . $alumno->numeroDepartamento : "") !!}{!! ((isset($alumno->referenciaDireccion) && $alumno->referenciaDireccion != "") ? " - " . $alumno->referenciaDireccion : "") !!}<br/>{{ $alumno->direccionUbicacion }}</p>
        <div class="sec-mapa">
          @include("util.ubicacionMapa", ["geoLatitud" => $alumno->geoLatitud, "geoLongitud" => $alumno->geoLongitud, "modo" => "ficha"])
        </div>
        <hr>   
        @if(isset($alumno->telefono))
        <strong><i class="fa fa-phone margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">
          {{ $alumno->telefono }}
        </p>
        <hr>
        @endif 
        @if(isset($alumno->idNivelIngles) && $alumno->idNivelIngles !== "")
        <strong><i class="fa fa-fw fa-list-ol"></i> Nivel de ingles</strong>
        <p class="text-muted">
          {{ App\Models\NivelIngles::listarSimple()[$alumno->idNivelIngles] }}
        </p>
        <hr> 
        @endif
        @if(isset($alumno->comentarioAdicional) && trim($alumno->comentarioAdicional) != "")
        <strong><i class="fa fa-fw fa-file-text"></i> Comentarios adicionales</strong>
        <p class="text-muted">
          {{ $alumno->comentarioAdicional }}
        </p>
        <hr>  
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

