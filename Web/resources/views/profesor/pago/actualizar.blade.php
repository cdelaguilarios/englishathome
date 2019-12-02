{{----}}
{{--*/ $idSeccion = "actualizar" /*--}}  
<script>
  var urlDatosPago = "{{ route('profesores.pagos.datos', ['id' => $profesor->id, 'idPago' => 0]) }}";  
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/pago/actualizar.js")}}"></script> 
<script>
  actualizarPagoProfesor.establecerIdSeccion("{{ $idSeccion }}");
</script>
<div id="sec-pago-actualizar" style="display: none">
{{ Form::open(["url" => route("profesores.pagos.generales.actualizar", ["id" => $profesor->id]), "id" => "formulario-pago-" . $idSeccion, "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("profesor.pago.formulario", ["idSeccion" => $idSeccion, "seccionActualizar" => 1])
{{ Form::close() }}
</div>