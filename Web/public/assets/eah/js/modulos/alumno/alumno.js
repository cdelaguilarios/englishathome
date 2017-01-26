var mapa;
var uto = null;

$(document).ready(function () {
  cargarLista();
  cargarFormulario();
  
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  $("#sel-alumno").select2();
  $("#sel-alumno").change(function () {
    if ($(this).data("seccion") === "perfil" && urlPerfil !== "") {
      window.location.href = urlPerfil.replace("/0", "/" + $(this).val());
    } else if (urlEditar !== "") {
      window.location.href = urlEditar.replace("/0", "/" + $(this).val());
    }
  });
});

function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlActualizarEstado = (typeof (urlActualizarEstado) === "undefined" ? "" : urlActualizarEstado);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosCambio = (typeof (estadosCambio) === "undefined" ? "" : estadosCambio);

  if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && urlActualizarEstado !== "" && urlEliminar !== "" && estados !== "" && estadosCambio !== "") {
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
      order: [[3, "desc"]],
      columns: [
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
          }},
        {data: "correoElectronico", name: "entidad.correoElectronico"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            if (estados[d.estado] !== undefined && estadosCambio[d.estado] !== undefined) {
              return '<div class="sec-btn-editar-estado"><a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span></a></div>';
            } else if (estados[d.estado] !== undefined) {
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
            } else {
              return "";
            }
          }, className: "text-center"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
        {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
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
      ]
    });
    $("#bus-estado").change(function () {
      $("#tab-lista").DataTable().ajax.reload();
    });
    $(window).click(function (e) {
      if (!$(e.target).closest(".sec-btn-editar-estado").length) {
        $(".sec-btn-editar-estado select").trigger("change");
      }
    });
    $(".btn-editar-estado").live("click", function () {
      $("#sel-estados").clone().val($(this).data("estado")).data("id", $(this).data("id")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado"));
      $(this).remove();
      event.stopPropagation();
    });
    $(".sec-btn-editar-estado select").live("change", function () {
      var id = $(this).data("id");
      if (urlActualizarEstado !== "" && $(this).data("estado") !== $(this).val()) {
        llamadaAjax(urlActualizarEstado.replace("/0", "/" + id), "POST", {"estado": $(this).val()}, true);
      }
      $(this).closest(".sec-btn-editar-estado").append('<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + id + '" data-estado="' + $(this).val() + '"><span class="label ' + estados[$(this).val()][1] + ' btn-estado">' + estados[$(this).val()][0] + '</span></a>');
      $(this).remove();
    });
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
        required: true
      },
      fechaNacimiento: {
        required: true,
        validarFecha: true
      },
      idTipoDocumento: {
        required: true
      },
      numeroDocumento: {
        required: true,
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
        if (confirm($("#btn-guardar").text().trim() === "Guardar datos"
            ? "¿Está seguro que desea guardar los cambios de los datos del alumno?"
            : "¿Está seguro que desea registrar estos datos?")) {
          $.blockUI({message: "<h4>" + ($("#btn-guardar").text().trim() === "Guardar datos" ? "Guardando" : "Registrando") + " datos...</h4>"});
          f.submit();
        }
      } else {
        agregarMensaje("advertencias", "Debe ingresar un horario disponible para sus clases", true, "#sec-men-alerta-horario");
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
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  if ($("input[name='modoEditarRegistrar']").val() === "1") {
    $("#wiz-registro-alumno").wizard();
    $("#wiz-registro-alumno").on("actionclicked.fu.wizard", function (e, data) {
      var campos = $("#formulario-alumno").find("#sec-wiz-alumno-" + data.step).find(":input, select");
      if (data.direction === "next" && !campos.valid()) {
        e.preventDefault();
      }
    }).on("changed.fu.wizard", function (evt, data) {
      google.maps.event.trigger(mapa, "resize");
      verificarPosicionSel();
    }).on("finished.fu.wizard", function (evt, data) {
      $("#formulario-alumno").submit();
    });

    var fechaNacimiento = $("#fecha-nacimiento").val();
    var fechaInicioClase = $("#fecha-inicio-clase").val();
    var numeroHorasClase = $("input[name='auxNumeroHorasClase']").val();

    if (!($("input[name='idInteresado']").length > 0 && $("input[name='idInteresado']").val() !== "")) {
      establecerCalendario("fecha-nacimiento", true, false);
    }
    establecerCalendario("fecha-inicio-clase", false, (fechaInicioClase === ""));
    establecerCampoDuracion("numero-horas-clase", (numeroHorasClase !== "" ? numeroHorasClase : undefined));

    if (fechaNacimiento !== "" && fechaInicioClase !== "") {
      $("#fecha-nacimiento").datepicker("setDate", (new Date(fechaNacimiento)));
      $("#fecha-inicio-clase").datepicker("setDate", (new Date(fechaInicioClase)));
    }
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
function verificarDatosBusquedaMapa() {
  if ($("#direccion").val() !== "" && $("#codigo-distrito option:selected").text() !== "" &&
      $("#codigo-provincia option:selected").text() !== "" && $("#codigo-departamento option:selected").text() !== "") {
    buscarDireccionMapa($("#direccion").val() + " " + $("#codigo-distrito option:selected").text() +
        ", " + $("#codigo-provincia option:selected").text() + ", " + $("#codigo-departamento option:selected").text());
  }
}


