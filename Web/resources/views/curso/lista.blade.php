@extends("layouts.master")
@section("titulo", "Cursos")

@section("section_script")
<script>
  var urlListar = "{{ route('cursos.listar') }}";
  var urlEditar = "{{ route('cursos.editar', ['id' => 0]) }}";
  var urlEliminar = "{{ route('cursos.eliminar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/curso.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Cursos</li>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de cursos</h3> 
        <a href="{{ route("cursos.crear")}}" class="btn btn-primary btn-clean">Nuevo curso</a>  
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre</th> 
              <th>Descripción</th>  
              <th>Activo</th>    
              <th class="all">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection