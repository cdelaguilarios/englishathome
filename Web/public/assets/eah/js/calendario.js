window.addEventListener("load", verificarJqueryCalendario, false);
function verificarJqueryCalendario() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionCalendario() : window.setTimeout(verificarJqueryCalendario, 100));
}

function  cargarSeccionCalendario() {
  if (!$("#sec-calendario").is(":visible")) {
    setTimeout(function () {
      cargarSeccionCalendario();
    }, 100);
  } else {
    $('#calendario').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listMonth'
      },
      locale: 'es',
      allDaySlot: false
    });
  }
}
