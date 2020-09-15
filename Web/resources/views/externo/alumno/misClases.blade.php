@extends("externo.layouts.master")
@section("titulo", "Mis clases")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route('alumnos.mis.clases.listar') }}";
  var urlConfirmar = "{{ route('alumnos.mis.clases.confirmar.clase', ['idClase' => 0]) }}";
  
  var estadoClaseConfirmadaProfesor = "{{ App\Helpers\Enum\EstadosClase::ConfirmadaProfesor }}";
  var estadoClaseConfirmadaProfesorAlumno = "{{ App\Helpers\Enum\EstadosClase::ConfirmadaProfesorAlumno }}";
  var estadoClaseRealizada = "{{ App\Helpers\Enum\EstadosClase::Realizada }}";
</script>
<script src="{{ asset("assets/eah/js/modulosExternos/alumno/misClases.js")}}"></script>
@endsection

@section("content")
@include("partials/errors")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">        
        <h3 class="box-title">Mis clases</h3>
      </div>         
      <div class="box-body">
        <table id="tab-lista-clases" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th>    
              <th class="all">Datos</th>
              <th>Avance</th>
              <th>Comentarios de {{ $nombreComercialEmpresa }}</th>
              <th>Confirmada</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@include("externo.alumno.util.formularioComentariosClase") 
@endsection
