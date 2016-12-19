@extends("layouts.master")
@section("titulo", "Profesores")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route("profesores.listar") }}";
  var urlPerfil = "{{ route("profesores.perfil", ["id" => 0]) }}";
  var urlEditar = "{{ route("profesores.editar", ["id" => 0]) }}";
  var urlEliminar = "{{ route("profesores.destroy", ["id" => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosProfesor::Listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/profesor.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Profesores</li>
@endsection

@section("content")
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de profesores</h3>
        <a href="{{ route("profesores.nuevo")}}" class="btn btn-primary btn-clean">Nuevo profesor</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Estado</th>
              <th class="col-md-1">&nbsp;</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
