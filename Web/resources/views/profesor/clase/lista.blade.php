{{----}}
<script>
  var urlListarClases = "{{ route('profesores.clases.listar', ['id' => $profesor->id]) }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/profesor/clase/lista.js")}}"></script>
<div id="sec-clase-lista" style="display: none">
  @include("util.listaClases")
</div>  