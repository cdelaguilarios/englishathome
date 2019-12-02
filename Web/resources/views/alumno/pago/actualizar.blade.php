{{----}}
{{--*/ $idSeccion = "actualizar" /*--}}  
<script>
  var urlDatosPago = "{{ route('alumnos.pagos.datos', ['id' => $alumno->id, 'idPago' => 0]) }}";  
  var motivoPagoClases = "{{ App\Helpers\Enum\MotivosPago::Clases }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/actualizar.js")}}"></script> 
<script>
  actualizarPagoAlumno.establecerIdSeccion("{{ $idSeccion }}");
</script>
<div id="sec-pago-actualizar" style="display: none">
{{ Form::open(["url" => route("alumnos.pagos.actualizar", ["id" => $alumno->id]), "id" => "formulario-pago-" . $idSeccion, "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("alumno.pago.formulario", ["idSeccion" => $idSeccion, "seccionActualizar" => 1])
{{ Form::close() }}
@include("alumno.util.docentesDisponibles", ["idSeccion" => "pago-" . $idSeccion, "idCurso" => (isset($alumno->idCurso) ? $alumno->idCurso : null)])
</div>