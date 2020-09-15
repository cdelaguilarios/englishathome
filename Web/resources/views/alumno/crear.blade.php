@extends("layouts.master")
@section("titulo", "Alumnos")

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
<script src="{{ asset("assets/eah/js/modulos/alumno/formulario.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("alumnos") }}">Alumnos</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@include("partials/errors")
@if(isset($vistaExterna) && $vistaExterna && isset($interesado) && in_array($interesado->estado, [App\Helpers\Enum\EstadosInteresado::FichaCompleta, App\Helpers\Enum\EstadosInteresado::AlumnoRegistrado]))
<div class="row text-center">
  <h4>
    @if(isset($nuevoRegistro) && $nuevoRegistro)
    Muchas gracias por registrar sus datos, pronto nos estaremos comunicando con usted.
    @else
    Usted ya está registrado como alumno(a) en nuestro sistema, para cualquier información comuníquese con nosotros al {{ App\Models\VariableSistema::obtenerXLlave("celularesEmpresa") }}.
    @endif
  </h4>
</div>
@else
{{ Form::open(["url" => route("alumnos.registrar" . (isset($vistaExterna) && $vistaExterna && isset($interesado) ? ".externo" : "")), "id" => "formulario-alumno", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("alumno.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endif
@endsection