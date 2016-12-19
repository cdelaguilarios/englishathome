@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route("interesados.listar") }}";
  var urlEditar = "{{ route("interesados.editar", ["id" => 0]) }}";
  var urlEliminar = "{{ route("interesados.destroy", ["id" => 0]) }}";
  var estados = {!!  json_encode(App\Helpers\Enum\EstadosInteresado::Listar()) !!};</script>
<script src="{{ asset("assets/eah/js/modulos/interesado.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Interesados</li>
@endsection

@section("content")
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de interesados</h3>
        <a href="{{ route("interesados.nuevo")}}" class="btn btn-primary btn-clean">Nueva persona interesada</a>   
      </div>         
      <div class="box-body">
        <table id="tab_lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Nombre completo</th>   
              <th>Teléfono</th>  
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
