window.addEventListener("load", verificarJqueryCalendario, false);
function verificarJqueryCalendario() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionCalendario() : window.setTimeout(verificarJqueryCalendario, 100));
}
//$("#calendario").fullCalendar('gotoDate', '2017-04-07');
function  cargarSeccionCalendario() {
  if (!$("#sec-calendario").is(":visible")) {
    setTimeout(function () {
      cargarSeccionCalendario();
    }, 100);
  } else {
    urlCalendario = (typeof (urlCalendario) === "undefined" ? "" : urlCalendario);
    if (urlCalendario !== "") {
      $("#sec-calendario").fullCalendar({
        header: {
          left: "prev,next today",
          center: "title",
          right: "month,agendaWeek,agendaDay,listMonth"
        },
        locale: 'es',
        allDaySlot: false,
        eventSources: [{
            url: urlCalendario,
            type: "POST",
            data: {
              _token: $("meta[name=_token]").attr("content")
            }
          }
        ],
        eventClick: function (ce, jse, v) {
          verDatosClase(ce.idAlumno, ce.id);
        }
      });
      establecerCalendario("bus-fecha-calendario", false, false, false, function () {
        var fechaBus = $("#bus-fecha-calendario").val();
        var datFechaBus = fechaBus.split("/");
        if (datFechaBus.length === 3) {
          $("#sec-calendario").fullCalendar("gotoDate", datFechaBus[2] + '-' + datFechaBus[1] + '-' + datFechaBus[0]);
        }
      });
    }
  }
}
