{{----}}
{{--*/ $idSeccion = "crear" /*--}}  
<script src="{{ asset("assets/eah/js/modulos/alumno/pago/crear.js")}}"></script>   
<script>
  crearPagoAlumno.establecerIdSeccion("{{ $idSeccion }}");
</script>
<div id="sec-pago-crear" style="display: none">
{{ Form::open(["url" => route("alumnos.pagos.registrar", ["id" => $alumno->id]), "id" => "formulario-pago-" . $idSeccion, "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("alumno.pago.formulario", ["idSeccion" => $idSeccion])
{{ Form::close() }}
@include("alumno.util.docentesDisponibles", ["idSeccion" => "pago-" . $idSeccion, "idCurso" => (isset($alumno->idCurso) ? $alumno->idCurso : null)])
</div>