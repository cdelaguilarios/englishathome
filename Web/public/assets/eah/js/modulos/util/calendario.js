var calendario = {};
calendario = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  function  cargarSeccion()/* - */ {
    if (!$("#sec-calendario").is(":visible")) {
      setTimeout(function () {
        cargarSeccion();
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
      establecerFiltrosBusqueda();
    }
  }
  function obtenerDatosFuenteCalendario()/* - */ {
    urlCalendario = (typeof (urlCalendario) === "undefined" ? "" : urlCalendario);
    var idAlumno = null;
    var idProfesor = null;
    
    if (!($("#bus-tipo-filtro-calendario").length > 0 && $("#bus-tipo-filtro-calendario").val() !== "0")) {
      idAlumno = ($("#bus-tipo-entidad-calendario").val() !== "0" ? null : $("#bus-sel-alumno-calendario").val());
      idProfesor = ($("#bus-tipo-entidad-calendario").val() !== "0" ? $("#bus-sel-profesor-calendario").val() : null);
    }
    
    return ((urlCalendario !== "") ?
            {
              url: urlCalendario,
              type: "POST",
              data: {
                _token: $("meta[name=_token]").attr("content"),
                tipoEntidad: $("#bus-tipo-entidad-calendario").val(),
                idAlumno: idAlumno,
                idProfesor: idProfesor
              }
            } : []);
  }

  function establecerFiltrosBusqueda()/* - */ {
    utilFechasHorarios.establecerCalendario($("#bus-fecha-calendario"), false, false, false, function () {
      var fechaBus = $("#bus-fecha-calendario").val();
      var datFechaBus = fechaBus.split("/");
      if (datFechaBus.length === 3) {
        $("#sec-calendario").fullCalendar("gotoDate", datFechaBus[2] + '-' + datFechaBus[1] + '-' + datFechaBus[0]);
      }
    });
    $("#bus-tipo-filtro-calendario").change(function () {
      if ($(this).val() !== "0") {
        $("#sec-filtro-entidad-calendario").hide();
      } else {
        $("#sec-filtro-entidad-calendario").show();
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
      urlBuscarAlumnos = (typeof (urlBuscarAlumnos) === "undefined" ? "" : urlBuscarAlumnos);
      urlBuscarProfesores = (typeof (urlBuscarProfesores) === "undefined" ? "" : urlBuscarProfesores);

      utilBusqueda.establecerListaBusqueda($("#bus-sel-alumno-calendario"), urlBuscarAlumnos);
      utilBusqueda.establecerListaBusqueda($("#bus-sel-profesor-calendario"), urlBuscarProfesores);
    }
    
    $("#bus-tipo-filtro-calendario, #bus-tipo-entidad-calendario, #bus-sel-alumno-calendario, #bus-sel-profesor-calendario").change(function () {
      if ($(this).val() !== this.options[this.selectedIndex].innerHTML) {
        var datosFuenteCalendario = obtenerDatosFuenteCalendario();
        $('#sec-calendario').fullCalendar('removeEventSource', datosFuenteCalendario);
        $('#sec-calendario').fullCalendar('addEventSource', datosFuenteCalendario);
      }
    });
  }
}());

