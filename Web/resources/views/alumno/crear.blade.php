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
  @media (max-width: 768px){
    input[type=file] {
      max-width: 200px;
    }
  }
</style>
@endsection
@endif

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/alumno/alumno.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("alumnos") }}">Alumnos</a></li>
<li class="active">Nuevo</li>
@endsection

@section("content") 
@if(isset($interesado) && $interesado->estado == App\Helpers\Enum\EstadosInteresado::AlumnoRegistrado)
<div class="row text-center">
  <h4>Muchas gracias por registrar tus datos, pronto nos estaremos comunicando con usted.</h4>
</div>
@else
@include("partials/errors")
{{ Form::open(["url" => "alumnos", "id" => "formulario-alumno", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("alumno.formulario", ["modo" => "registrar"])
{{ Form::close() }}
@endif
@endsection