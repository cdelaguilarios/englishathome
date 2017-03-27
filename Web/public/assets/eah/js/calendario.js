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
    urlListarCalendario = (typeof (urlListarCalendario) === "undefined" ? "" : urlListarCalendario);
    if (urlListarCalendario !== "") {
      $('#calendario').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay,listMonth'
        },
        locale: 'es',
        allDaySlot: false,
        eventSources: [{
            url: urlListarCalendario,
            type: 'POST',
            data: {
              _token: $('meta[name=_token]').attr("content")
            }
          }
        ]
      });
    }
    //var view = $('#calendario').fullCalendar('getView');
    //view.start._d
    //view.end._d
  }
}
