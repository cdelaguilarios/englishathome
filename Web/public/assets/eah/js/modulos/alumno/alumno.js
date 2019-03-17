var mapa;

$(document).ready(function () {
  cargarLista();
  cargarFormulario();

  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
  idAlumno = (typeof (idAlumno) === "undefined" ? "" : idAlumno);
  nombreCompletoAlumno = (typeof (nombreCompletoAlumno) === "undefined" ? "" : nombreCompletoAlumno);
  establecerListaBusqueda("#sel-alumno", urlBuscar);
  $("#sel-alumno").empty().append('<option value="' + idAlumno + '">' + nombreCompletoAlumno + '</option>').val(idAlumno);
  $("#sel-alumno").change(function () {
    if ($(this).data("seccion") === "perfil" && urlPerfil !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML)
      window.location.href = urlPerfil.replace("/0", "/" + $(this).val());
    else if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML)
      window.location.href = urlEditar.replace("/0", "/" + $(this).val());
  });
});

function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlActualizarEstado = (typeof (urlActualizarEstado) === "undefined" ? "" : urlActualizarEstado);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  urlHorario = (typeof (urlHorario) === "undefined" ? "" : urlHorario);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosCambio = (typeof (estadosCambio) === "undefined" ? "" : estadosCambio);
  estadoCuotaProgramada = (typeof (estadoCuotaProgramada) === "undefined" ? "" : estadoCuotaProgramada);

  if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && urlActualizarEstado !== "" && urlEliminar !== "" && urlPerfilProfesor !== "" && urlHorario !== "" && estados !== "" && estadosCambio !== "" && estadoCuotaProgramada !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.estado = $("#bus-estado").val();
        }
      },
      autoWidth: false,
      responsive: true,
      orderCellsTop: true,
      fixedHeader: true,
      order: [[2, "asc"]],
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile",
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }},
        {data: "nombre", name: "entidad.nombre", width: "20%", render: function (e, t, d, m) {
            return '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>' + (d.distritoAlumno ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + letraCapital(d.distritoAlumno) + '</span>' : '') + (d.nombreProfesor ? '<br/><br/>Profesor(a): <a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' + (d.distritoProfesor ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + letraCapital(d.distritoProfesor) + '</span>' : '') : '');
          }},
        {data: "porcentajeAvanceClases", name: "porcentajeAvanceClases", width: "22%", render: function (e, t, d, m) {
            if (d.duracionTotalClases) {
              var porcentajeAvance = (d.duracionTotalClasesRealizadas ? d.porcentajeAvanceClases : 0);
              return '<div class="clearfix"><span class="pull-left">Total de clases: ' + d.totalClases + '</span><small class="pull-right">' + redondear(porcentajeAvance, 2) + '%</small></div><div class="progress xs"><div class="progress-bar progress-bar-green" style="width: ' + porcentajeAvance + '%;"></div></div><div class="clearfix"><span class="pull-left"><span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas"><i class="fa fa-clock-o"></i> ' + formatoHora(d.duracionTotalClasesRealizadas) + '</span>  de  <span class="text-info" data-toggle="tooltip" title="" data-original-title="Horas programadas"><i class="fa fa-clock-o"></i> ' + formatoHora(d.duracionTotalClases) + '</span></span></div>';
            } else {
              return 'Sin clases registradas';
            }
          }},
        {data: "curso", name: "curso", render: function (e, t, d, m) {
            /*llamadaAjax(urlHorario.replace("/0", "/" + d.id), "POST", {}, true, function (datos) {
              $("#sec-info-horario-" + datos.idEntidad).html(obtenerTextoHorario($.parseJSON(datos.datosHorario)));
            });*/
            return d.curso /*+ '<div id="sec-info-horario-' + d.id + '"></div>'*/;
          }},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            var estado = '';
            if (estados[d.estado] !== undefined && estadosCambio[d.estado] !== undefined) {
              estado = '<div class="sec-btn-editar-estado"><a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span></a></div>' + (d.estado === estadoCuotaProgramada && d.fechaUltimaClase ? '<small class="text-red">(Última clase: ' + formatoFecha(d.fechaUltimaClase) + ')</small><br/>' : '');
            } else if (estados[d.estado] !== undefined) {
              estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' + (d.estado === estadoCuotaProgramada && d.fechaUltimaClase ? '<small class="text-red">(Última clase: ' + formatoFecha(d.fechaUltimaClase) + ')</small><br/>' : '');
            }
            return estado + '<span class="text-info">(Nivel ' + d.nivelIngles + ')</span>';
          }, className: "text-center"},
        {data: "totalPagos", name: "totalPagos", render: function (e, t, d, m) {
            return 'S/. ' + redondear(d.pagoAcumulado, 2) + '<br/><span class="text-info">(' + d.totalPagos + ' pago' + (d.totalPagos > 1 ? 's' : '') + ')</span>';
          }, className: "text-center"},
        {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar alumno" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este alumno?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
        establecerCabecerasBusquedaTabla("tab-lista");
      }
    });
  }
  if (urlActualizarEstado !== "" && estados !== "") {
    establecerCambiosBusquedaEstados("tab-lista", urlActualizarEstado, estados, true);
  }
}

function cargarFormulario() {
  minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
  maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);
  urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);
  $("#formulario-alumno").validate({
    ignore: "",
    rules: {
      nombre: {
        required: true,
        validarAlfabetico: true
      },
      apellido: {
        required: true,
        validarAlfabetico: true
      },
      telefono: {
        required: ($("input[name='usuarioNoLogueado']").val() === "1")
      },
      fechaNacimiento: {
        required: ($("input[name='usuarioNoLogueado']").val() === "1"),
        validarFecha: true
      },
      idTipoDocumento: {
        required: true
      },
      numeroDocumento: {
        required: ($("input[name='usuarioNoLogueado']").val() === "1"),
        number: true
      },
      correoElectronico: {
        required: true,
        email: true
      },
      imagenPerfil: {
        validarImagen: true
      },
      codigoDepartamento: {
        required: true
      },
      codigoProvincia: {
        required: true
      },
      codigoDistrito: {
        required: true
      },
      direccion: {
        required: true
      },
      numeroHorasClase: {
        required: true,
        validarDecimal: true,
        range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
      },
      fechaInicioClase: {
        required: true,
        validarFecha: true
      },
      costoHoraClase: {
        required: true,
        validarDecimal: true
      }
    },
    submitHandler: function (f) {
      if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
        if (confirm($("input[name='modoEditar']").val() === "1"
            ? "¿Está seguro que desea guardar los cambios de los datos del alumno?"
            : "¿Está seguro que desea registrar estos datos?")) {
          $.blockUI({message: "<h4>" + ($("input[name='modoEditar']").val() === "1" ? "Guardando" : "Registrando") + " datos...</h4>"});
          f.submit();
        }
      } else {
        agregarMensaje("advertencias", "Debe ingresar un horario disponible para sus clases.", true, "#sec-men-alerta-horario");
      }
    },
    highlight: function () {
    },
    unhighlight: function () {
    },
    errorElement: "div",
    errorClass: "help-block-error",
    errorPlacement: function (error, element) {
      if (element.closest("div[class*=col-sm-]").length > 0) {
        element.closest("div[class*=col-sm-]").append(error);
      } else if (element.parent(".input-group").length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    },
    invalidHandler: function (e, v) {
      if (v.errorList.length > 0 && $(v.errorList[0].element).closest(".step-pane").data("step") !== undefined) {
        $('#wiz-registro-alumno').wizard('selectedItem', {step: $(v.errorList[0].element).closest(".step-pane").data("step")});
      }
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  if ($("input[name='modoEditarRegistrar']").val() === "1") {
    establecerWizard("alumno", ($("input[name='modoEditar']").length > 0 && $("input[name='modoEditar']").val() === "1"));

    if (!($("input[name='idInteresado']").length > 0 && $("input[name='idInteresado']").val() !== "")) {
      var fechaNacimiento = $("#fecha-nacimiento").val();
      establecerCalendario("fecha-nacimiento", false, true, false);
      if (fechaNacimiento !== "") {
        if (Date.parse(fechaNacimiento)) {
          var datFechaNacimiento = fechaNacimiento.split("/");
          $("#fecha-nacimiento").datepicker("setDate", (new Date(datFechaNacimiento[1] + "/" + datFechaNacimiento[0] + "/" + datFechaNacimiento[2])));
        } else {
          $("#fecha-nacimiento").datepicker("setDate", (new Date()));
        }
      }
    }

    var fechaInicioClase = $("#fecha-inicio-clase").val();
    establecerCalendario("fecha-inicio-clase", false, false, false);
    if (fechaInicioClase !== "") {
      if (Date.parse(fechaInicioClase)) {
        var datFechaInicioClase = fechaInicioClase.split("/");
        $("#fecha-inicio-clase").datepicker("setDate", (new Date(datFechaInicioClase[1] + "/" + datFechaInicioClase[0] + "/" + datFechaInicioClase[2])));
      } else {
        $("#fecha-inicio-clase").datepicker("setDate", (new Date()));
      }
    }

    var numeroHorasClase = $("input[name='auxNumeroHorasClase']").val();
    establecerCampoDuracion("numero-horas-clase", (numeroHorasClase !== "" ? numeroHorasClase : 7200));

    $("#direccion").focusout(verificarDatosBusquedaMapa);
    $("input[name='codigoUbigeo']").change(verificarDatosBusquedaMapa);
  } else {
    $("input[name='horario']").change(function () {
      if (urlActualizarHorario !== "" && $(this).val() !== "") {
        $.blockUI({message: "<h4>Actualizando horario...</h4>"});
        llamadaAjax(urlActualizarHorario, "POST", {"horario": $(this).val()}, true,
            function (d) {
              $("body").unblock({
                onUnblock: function () {
                  agregarMensaje("exitosos", "Actualización de horario exitosa.", true);
                }
              });
            },
            function (d) {
            },
            function (de) {
              $("body").unblock({
                onUnblock: function () {
                  agregarMensaje("errores", "Ocurrió un problema durante la actualización del horario del alumno. Por favor inténtelo nuevamente.", true);
                }
              });
            }
        );
      }
    });
  }
}


