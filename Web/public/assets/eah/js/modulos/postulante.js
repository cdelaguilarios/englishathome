$(document).ready(function () {
  cargarLista();
  cargarFormulario();

  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  $("#sel-postulante").select2();
  $("#sel-postulante").change(function () {
    if (urlEditar !== "") {
      window.location.href = urlEditar.replace("/0", "/" + $(this).val());
    }
  });
});

function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlPerfilProfesorPostulante = (typeof (urlPerfilProfesorPostulante) === "undefined" ? "" : urlPerfilProfesorPostulante);
  urlActualizarEstado = (typeof (urlActualizarEstado) === "undefined" ? "" : urlActualizarEstado);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosCambio = (typeof (estadosCambio) === "undefined" ? "" : estadosCambio);
  estadoProfesorRegistrado = (typeof (estadoProfesorRegistrado) === "undefined" ? "" : estadoProfesorRegistrado);

  if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && urlPerfilProfesorPostulante !== "" && urlActualizarEstado !== "" && urlEliminar !== "" && estados !== "" && estadosCambio !== "" && estadoProfesorRegistrado !== "") {
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
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' + ((d.estado === estadoProfesorRegistrado) ? '<a href="' + (urlPerfilProfesorPostulante.replace("/0", "/" + d.id)) + '" title="Ver perfil del profesor" target="_blank" class="btn-perfil-relacion-entidad"><i class="fa fa-eye"></i></a>' : '');
            } else {
              return "";
            }
          }, className: "text-center"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
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
                '<a href="javascript:void(0);" title="Eliminar alumno" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este postulante?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
      }
    });
  }
  if (urlActualizarEstado !== "" && estados !== "") {
    establecerCambiosBusquedaEstados("tab-lista", urlActualizarEstado, estados);
  }
}

function cargarFormulario() {
  formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);
  urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);
  $("#formulario-postulante").validate({
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
      fechaNacimiento: {
        required: formularioExternoPostulante,
        validarFecha: true
      },
      idTipoDocumento: {
        required: true
      },
      numeroDocumento: {
        required: formularioExternoPostulante,
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
      ultimosTrabajos: {
        required: formularioExternoPostulante
      },
      experienciaOtrosIdiomas: {
        required: formularioExternoPostulante
      },
      descripcionPropia: {
        required: formularioExternoPostulante
      },
      ensayo: {
        required: formularioExternoPostulante
      },
      "idCursos[]": {
        required: !formularioExternoPostulante
      },
      audio: {
        validarAudio: true,
        archivoTamanho: 2097152
      }
    },
    messages: {
      audio: {
        archivoTamanho: (formularioExternoPostulante ? "File must be less than 2MB" : "Archivo debe ser menor a 2MB.")
      }
    },
    submitHandler: function (f) {
      if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
        var mensajeConfirmacion = "¿Está seguro que desea registrar a este postulante como un nuevo profesor?";
        if ($("input[name='registrarComoProfesor']").val() !== "1") {
          mensajeConfirmacion = ($("input[name='modoEditar']").val() === "1"
              ? "¿Está seguro que desea guardar los cambios de los datos del postulante?"
              : (formularioExternoPostulante ? "Are you sure you want to register this data?" : "¿Está seguro que desea registrar estos datos?"));
        }
        if (confirm(mensajeConfirmacion)) {
          $.blockUI({message: "<h4>" + ($("input[name='modoEditar']").val() === "1" ? "Guardando datos..." : (formularioExternoPostulante ? "Saving Your Data" : "Registrando datos...")) + "</h4>"});
          f.submit();
        }
      } else {
        agregarMensaje("advertencias", (formularioExternoPostulante ? "Must enter your schedule available to work" : "Debe ingresar un horario disponible"), true, "#sec-men-alerta-horario");
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
        $('#wiz-registro-postulante').wizard('selectedItem', {step: $(v.errorList[0].element).closest(".step-pane").data("step")});
      }
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });

  if ($("input[name='modoEditarRegistrar']").val() === "1") {
    establecerWizard("postulante", ($("input[name='modoEditar']").length > 0 && $("input[name='modoEditar']").val() === "1"));

    if (!formularioExternoPostulante) {
      var fechaNacimiento = $("#fecha-nacimiento").val();
      establecerCalendario("fecha-nacimiento", false, true, false);
      if (fechaNacimiento !== "") {
        var datFechaNacimiento = fechaNacimiento.split("/");
        $("#fecha-nacimiento").datepicker("setDate", (new Date(datFechaNacimiento[1] + "/" + datFechaNacimiento[0] + "/" + datFechaNacimiento[2])));
      }
    }

    $("#curso-interes").select2();
    $("#direccion").focusout(verificarDatosBusquedaMapa);
    $("input[name='codigoUbigeo']").change(verificarDatosBusquedaMapa);

    if ($("input[name='cursos']").val() !== undefined && $("input[name='cursos']").val() !== "") {
      var selCursosVal = [];
      $.each(JSON.parse($("input[name='cursos']").val()), function (i, v) {
        if (v.idCurso !== undefined) {
          selCursosVal.push(v.idCurso);
        }
      });
      $("#curso-interes").val(selCursosVal).trigger("change");
    }

    $("#btn-registrar-profesor").click(function () {
      $("input[name='registrarComoProfesor']").val("1");
      $("#formulario-postulante").submit();
    });
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
                  agregarMensaje("errores", "Ocurrió un problema durante la actualización del horario del postulante. Por favor inténtelo nuevamente.", true);
                }
              });
            }
        );
      }
    });
  }
}


