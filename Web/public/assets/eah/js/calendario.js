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
    var datosFuenteCalendario = obtenerDatosFuenteCalendario();
    $("#sec-calendario").fullCalendar({
      header: {
        left: "prev,next today",
        center: "title",
        right: "month,agendaWeek,agendaDay,listMonth"
      },
      locale: 'es',
      allDaySlot: false,
      eventSources: [datosFuenteCalendario],
      eventClick: function (ce, jse, v) {
        verDatosClase(ce.idAlumno, ce.id);
      },
      contentHeight: 400
    });
    establecerCalendario("bus-fecha-calendario", false, false, false, function () {
      var fechaBus = $("#bus-fecha-calendario").val();
      var datFechaBus = fechaBus.split("/");
      if (datFechaBus.length === 3) {
        $("#sec-calendario").fullCalendar("gotoDate", datFechaBus[2] + '-' + datFechaBus[1] + '-' + datFechaBus[0]);
      }
    });
    $("#bus-tipo-entidad-calendario").change(function () {
      if ($(this).val() !== "0") {
        $("#sec-bus-sel-alumno-calendario").hide();
        $("#sec-bus-sel-profesor-calendario").show();
      } else {
        $("#sec-bus-sel-alumno-calendario").show();
        $("#sec-bus-sel-profesor-calendario").hide();
      }
    });
    if ($("#bus-tipo-entidad-calendario").attr("type") !== "hidden") {
      $("#bus-sel-alumno-calendario").select2();
      $("#bus-sel-profesor-calendario").select2();
    }
    $("#bus-tipo-entidad-calendario, #bus-sel-alumno-calendario, #bus-sel-profesor-calendario").change(function () {
      var datosFuenteCalendario = obtenerDatosFuenteCalendario();
      $('#sec-calendario').fullCalendar('removeEventSource', datosFuenteCalendario);
      $('#sec-calendario').fullCalendar('addEventSource', datosFuenteCalendario);
    });
  }
}

function obtenerDatosFuenteCalendario() {
  urlCalendario = (typeof (urlCalendario) === "undefined" ? "" : urlCalendario);
  return ((urlCalendario !== "") ?
      {
        url: urlCalendario,
        type: "POST",
        data: {
          _token: $("meta[name=_token]").attr("content"),
          tipoEntidad: $("#bus-tipo-entidad-calendario").val(),
          idAlumno: ($("#bus-tipo-entidad-calendario").val() !== "0" ? null : $("#bus-sel-alumno-calendario").val()),
          idProfesor: ($("#bus-tipo-entidad-calendario").val() !== "0" ? $("#bus-sel-profesor-calendario").val() : null)
        }
      } : []);
}
