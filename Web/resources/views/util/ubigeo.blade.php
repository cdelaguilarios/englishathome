<div class="form-group">
  {{ Form::label("codigo-departamento", "Ubicación: ", ["class" => "col-sm-2 control-label"]) }}
  <div class="col-sm-3">
    {{ Form::select("codigoDepartamento", [], null, ["id" => "codigo-departamento", "class" => "form-control"]) }}
  </div>
  <div class="col-sm-3">
    {{ Form::select("codigoProvincia", [], null, ["id" => "codigo-provincia", "class" => "form-control"]) }}
  </div>
  <div class="col-sm-3">
    {{ Form::select("codigoDistrito", [], null, ["id" => "codigo-distrito", "class" => "form-control"]) }}
  </div>
</div>
{{ Form::hidden("codigoUbigeo") }} 
<script>
  var urlListarDepartamentos = "{{ route('ubigeo.listarDepartamentos') }}";
  var urlListarProvincias = "{{ route('ubigeo.listarProvincias', ['codigoDepartamento' => 0]) }}";
  var urlListarDistritos = "{{ route('ubigeo.listarDistritos', ['codigoProvincia' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/ubigeo.js")}}"></script>