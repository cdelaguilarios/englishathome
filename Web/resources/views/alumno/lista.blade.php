@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route("alumnos.listar") }}";
  var urlPerfil = "{{ route("alumnos.perfil", ["id" => 0]) }}";
  var urlEditar = "{{ route("alumnos.editar", ["id" => 0]) }}";
  var urlEliminar = "{{ route("alumnos.destroy", ["id" => 0]) }}";
  var estados = {!! json_encode(App\Helpers\Enum\EstadosAlumno::Listar()) !!};
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/alumno.js") }}"></script>
@endsection

@section("breadcrumb")
<li class="active">Alumnos</li>
@endsection

@section("content")
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de alumnos</h3>
        <a href="{{ route("alumnos.nuevo")}}" class="btn btn-primary btn-clean">Nuevo alumno</a>   
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>     
              <th>Correo electrónico</th>
              <th>Estado</th>
              <th class="col-sm-1">&nbsp;</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
