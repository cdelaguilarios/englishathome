{{----}}
{{--*/ $idSeccion = "crear" /*--}}  
<script src="{{ asset("assets/eah/js/modulos/profesor/pago/crear.js")}}"></script>   
<script>
  crearPagoProfesor.establecerIdSeccion("{{ $idSeccion }}");
</script>
<div id="sec-pago-crear" style="display: none">
{{ Form::open(["url" => route("profesores.pagos.generales.registrar", ["id" => $profesor->id]), "id" => "formulario-pago-" . $idSeccion, "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("profesor.pago.formulario", ["idSeccion" => $idSeccion])
{{ Form::close() }}
</div>