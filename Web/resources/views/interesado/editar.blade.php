@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script>
  var urlEditar = "{{ route('interesados.editar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/interesado.js")}}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("interesados") }}">Interesados</a></li>
<li class="active">Editar</li>
@endsection

@section("content") 
@include("partials/errors")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-8">
            <a href="{{ route("interesados.crear")}}" class="btn btn-primary btn-clean">Nuevo interesado</a>
            @if($interesado->estado != App\Helpers\Enum\EstadosInteresado::AlumnoRegistrado)
            <button id="btn-registrar-alumno"  type="button" class="btn btn-primary" ><i class="fa fa-mortar-board"></i> Registrar como alumno</button>
            @endif
            <a href="{{ route("interesados.cotizar", ["id" => $interesado->idEntidad]) }}" type="button" class="btn btn-primary" ><i class="fa fa-dollar"></i> Enviar cotización</a>
          </div>           
          <div class="col-sm-4">
            {{ Form::select("",App\Models\Interesado::listarBusqueda(), $interesado->id, ["id"=>"sel-interesado", "class" => "form-control", "data-seccion" => "editar", "style" => "width: 100%;"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{ Form::model($interesado, ["method" => "PATCH", "action" => ["InteresadoController@actualizar", $interesado->id], "id" => "formulario-interesado", "class" => "form-horizontal", "files" => true]) }}
@include("interesado.formulario")
{{ Form::close() }}
@endsection
