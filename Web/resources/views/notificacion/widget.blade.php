<li class="dropdown notifications-menu">
  <a id="btn-ver-nuevas-notificaciones" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-bell-o"></i>
    <span id="sec-total-nuevas-notificaciones" class="label label-warning"></span>
  </a>
  <ul class="dropdown-menu">
    <li id="sec-titulo-nuevas-notificaciones" class="header"></li>
    <li>
      <ul id="sec-lista-nuevas-notificaciones" class="menu"></ul>
    </li>
    <li class="footer">
      <a href="{{ route("historial.notificaciones") }}">Ver todas</a>
    </li>
  </ul>
</li>
<script>
  var urlListaNotificaciones = "{{ route('historial.notificaciones') }}";
  var urlNuevasNotificaciones = "{{ route('historial.notificaciones.nuevas') }}";
  var urlRevisarNuevasNotificaciones = "{{ route('historial.notificaciones.nuevas.revisar') }}";
</script>
<script type="text/javascript" src="{{ asset("assets/eah/js/notificaciones.js")}}"></script>