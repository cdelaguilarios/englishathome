{{--*/ $cuentasBancariasReg = ""; /*--}} 
@if(isset($cuentasBancarias))
@foreach($cuentasBancarias as $cuentaBancaria)
{{--*/ $cuentasBancariasReg .= $cuentaBancaria->banco . "|" . $cuentaBancaria->numeroCuenta . ";"; /*--}}
@endforeach 

<script>
  var cuentasBancariasReg = {!!  json_encode($cuentasBancarias) !!};
</script>
@endif
<script src="{{ asset("assets/eah/js/modulos/util/cuentasBancarias.js")}}"></script>
<div class="form-group">
  <h4>Cuentas bancarias:</h4>
</div>
<div class="form-group">
  {{ Form::label("cuentaBancariaBanco", "Banco: ", ["class" => "col-sm-2 control-label"]) }}
  <div class="col-sm-3">
    {{ Form::text("cuentaBancariaBanco", null, ["class" => "form-control", "maxlength" =>"255"]) }}
  </div>
  {{ Form::label("cuentaBancariaNumero", "Número: ", ["class" => "col-sm-1 control-label"]) }}
  <div class="col-sm-3">
    {{ Form::text("cuentaBancariaNumero", null, ["class" => "form-control", "maxlength" =>"255"]) }}
  </div>
  <div class="col-sm-1">
    <button id="cuenta-bancaria-agregar" type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>
  </div>
  {{ Form::hidden("cuentasBancarias", $cuentasBancariasReg) }} 
</div>
<div id="cuentas-bancarias-lista"></div>