@extends("layouts.master")
@section("titulo", "Ficha " . ($profesor->sexo == "F" ? "de la profesora" : "del profesor") . " " . $profesor->nombre . " " .  $profesor->apellido)

@section("section_script")
@if(isset($impresionDirecta) && $impresionDirecta)
<script>
  window.onafterprint = function (e) {
    $(window).off('mousemove', window.onafterprint);
    window.close();
  };
  window.print();
  setTimeout(function () {
    $(window).one('mousemove', window.onafterprint);
  }, 500);
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
</style>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-offset-1 col-sm-10">
    <div class="sec-datos">
      <div class="box-body">
        <strong><i class="fa fa-fw fa-envelope"></i> Correo electrónico</strong>
        <p class="text-muted">
          {{ $profesor->correoElectronico }}
          @include("util.imagenPerfil", ["entidad" => $profesor])
        </p>
        <hr> 
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
@endsection

