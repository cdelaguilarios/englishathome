@extends("layouts.master")
@section("titulo", "Postulantes")

@if(isset($vistaExterna) && $vistaExterna)
@section("section_style")
<style>
  .register-box .steps{
    display:none;
  }
  .register-box .box-footer {
    border-top: 0;
    padding: 10px;
    background-color: inherit;
  }
  .register-box .wizard {
    background-color: #ffffff;
  }
  .register-box .vcenter {
    display: inline-block;
    vertical-align: middle;
    float: none;
  }
</style>
@endsection
@endif

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/postulante.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("postulantes") }}">Postulantes</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
@if(isset($vistaExterna) && $vistaExterna && isset($nuevoRegistro) && $nuevoRegistro)
<div class="row text-center">
  <h4>    
    Muchas gracias por registrar sus datos, pronto nos estaremos comunicando con usted.
  </h4>
</div>
@else
{{ Form::open(["url" => route("postulantes.registrar" . (isset($vistaExterna) && $vistaExterna ? ".externo" : "")), "id" => "formulario-postulante", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("postulante.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endif
@endsection