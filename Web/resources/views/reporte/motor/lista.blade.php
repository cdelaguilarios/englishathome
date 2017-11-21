@extends("layouts.master")
@section("titulo", "Motor de reportes")

@section("section_script")
<script>
  var urlListar = "{{ route('reportes.listar') }}";
  var urlEditar = "{{ route('reportes.editar', ['id' => 0]) }}";
  var urlEliminar = "{{ route('reportes.eliminar', ['id' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/reporte/motor/lista.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Motor de reportes</li>
@endsection

@section("content")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de reportes</h3> 
        <a href="{{ route("reportes.crear")}}" class="btn btn-primary btn-clean">Nuevo reporte</a>  
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Título</th> 
              <th>Descripción</th>   
              <th class="all">Opciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection