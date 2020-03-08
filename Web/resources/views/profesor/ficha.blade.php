{{----}}
@extends("layouts.master")
@section("titulo", "Ficha " . ($profesor->sexo == "F" ? "de la profesora" : "del profesor") . " " . $profesor->nombre . " " .  $profesor->apellido)

@section("tituloImpresion")
<b>Ficha {{ $profesor->sexo == "F" ? "de la profesora" : "del profesor" }}<br/>{{ $profesor->nombre . " " .  $profesor->apellido }}<br/><small><i class="fa fa-fw fa-envelope"></i> {{ $profesor->correoElectronico }}<br/><i class="fa fa-fw fa-phone"></i> {{ $profesor->telefono }}</small></b>
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
            @include("util.horario", ["horario" => $profesor->horario, "modo" => "visualizar"])
            @include("util.imagenPerfil", ["entidad" => $profesor])
          </p>
          <hr>   
          @if(isset($profesor->cursos) && count($profesor->cursos) > 0)
          <strong><i class="fa fa-fw flaticon-favorite-book"></i> Cursos</strong>
          @foreach($profesor->cursos as $curso)
          @if(App\Models\Curso::verificarExistencia($curso->idCurso))
          <p class="text-muted">- {{ App\Models\Curso::listarSimple(FALSE)[$curso->idCurso] }}</p>
          @endif
          @endforeach
          <hr> 
          @endif
          <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
          <p class="text-muted">{{ $profesor->direccion }}{!! ((isset($profesor->numeroDepartamento) && $profesor->numeroDepartamento != "") ? "<br/>Depto./Int " . $profesor->numeroDepartamento : "") !!}{!! ((isset($profesor->referenciaDireccion) && $profesor->referenciaDireccion != "") ? " - " . $profesor->referenciaDireccion : "") !!}<br/>{{ $profesor->direccionUbicacion }}</p>
          <div class="sec-mapa">
            <img id="img-mapa" src="http://maps.google.com/maps/api/staticmap?sensor=false&center={{ $profesor->geoLatitud }},{{ $profesor->geoLongitud }}&zoom=17&size=800x550&markers=color:0x3C8DBC|{{ $profesor->geoLatitud }},{{ $profesor->geoLongitud }}&key={{ Config::get("eah.apiKeyGoogleMaps") }}" />
          </div>
          <hr>  
        </div> 
        <div>  
          @if(isset($profesor->comentarioAdministrador) && trim($profesor->comentarioAdministrador) != "")
          <strong><i class="fa fa-fw fa-file-text"></i> Comentarios del administrador</strong>
          <p class="text-muted">
            {!! $profesor->comentarioAdministrador !!}
          </p>
          <hr>  
          @endif 
          @if(isset($profesor->comentarioPerfil) && trim($profesor->comentarioPerfil) != "")
          <strong><i class="fa fa-fw fa-file-text"></i> Perfil</strong>
          <p class="text-muted">
            {!! $profesor->comentarioPerfil !!}
          </p>
          <hr>  
          @endif 
        </div>       
      </div>
    </div>
  </div>
</div>
@endsection

