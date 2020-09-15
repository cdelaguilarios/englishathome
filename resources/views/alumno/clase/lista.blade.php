<script>
  var urlListarClases = "{{ route('alumnos.clases.listar', ['id' => $alumno->id]) }}";
  var urlEliminarClase = "{{ route('alumnos.clases.eliminar', ['id' => $alumno->id, 'idClase' => 0]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/alumno/clase/lista.js")}}"></script>
<div id="sec-clase-lista" style="display: none">
  @include("util.listaClases")
</div>  