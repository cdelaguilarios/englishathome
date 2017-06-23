$(document).ready(function () {
  cargarLista();
  cargarFormulario();
  cargarFormularioCotizacion();

  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlCotizar = (typeof (urlCotizar) === "undefined" ? "" : urlCotizar);
  $("#sel-interesado").select2();
  $("#sel-interesado").change(function () {
    if ($(this).data("seccion") === "cotizar" && urlCotizar !== "") {
      window.location.href = urlCotizar.replace("/0", "/" + $(this).val());
    } else if (urlEditar !== "") {
      window.location.href = urlEditar.replace("/0", "/" + $(this).val());
    }
  });
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlCotizar = (typeof (urlCotizar) === "undefined" ? "" : urlCotizar);
  urlPerfilAlumnoInteresado = (typeof (urlPerfilAlumnoInteresado) === "undefined" ? "" : urlPerfilAlumnoInteresado);
  urlActualizarEstado = (typeof (urlActualizarEstado) === "undefined" ? "" : urlActualizarEstado);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosCambio = (typeof (estadosCambio) === "undefined" ? "" : estadosCambio);
  estadoAlumnoRegistrado = (typeof (estadoAlumnoRegistrado) === "undefined" ? "" : estadoAlumnoRegistrado);
  if (urlListar !== "" && urlEditar !== "" && urlCotizar !== "" && urlPerfilAlumnoInteresado !== "" && urlEliminar !== "" && estados !== "" && estadosCambio !== "" && estadoAlumnoRegistrado !== "") {
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
      order: [[4, "desc"]],
      columns: [
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
          }},
        {data: "telefono", name: "entidad.telefono"},
        {data: "correoElectronico", name: "entidad.correoElectronico"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            if (estados[d.estado] !== undefined && estadosCambio[d.estado] !== undefined) {
              return '<div class="sec-btn-editar-estado"><a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span></a></div>';
            } else if (estados[d.estado] !== undefined) {
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' + ((d.estado === estadoAlumnoRegistrado) ? '<a href="' + (urlPerfilAlumnoInteresado.replace("/0", "/" + d.id)) + '" title="Ver perfil del alumno" target="_blank" class="btn-perfil-relacion-entidad"><i class="fa fa-eye"></i></a>' : '');
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
                '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="' + (urlCotizar.replace("/0", "/" + d.id)) + '" title="Enviar cotización"><i class="fa fa-envelope"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar interesado" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta persona interesada?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
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
    establecerCambiosBusquedaEstados("tab-lista", urlActualizarEstado, estados);
  }
}

function cargarFormulario() {
  $("#formulario-interesado").validate({
    ignore: ":hidden",
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
      correoElectronico: {
        required: true,
        email: true
      },
      idCurso: {
        required: true
      },
      costoHoraClase: {
        required: true,
        validarDecimal: true
      }
    },
    submitHandler: function (f) {
      var mensajeConfirmacion = "¿Está seguro que desea registrar a esta persona interesada como un nuevo alumno?";
      if ($("input[name='registrarComoAlumno']").val() !== "1") {
        mensajeConfirmacion = ($("#btn-guardar").text().trim() === "Guardar"
            ? "¿Está seguro que desea guardar los cambios de los datos de la persona interesada?"
            : "¿Está seguro que desea registrar los datos de esta persona interesada?");
      }
      if (confirm(mensajeConfirmacion)) {
        $.blockUI({message: "<h4>" + ($("#btn-guardar").text().trim() === "Guardar" ? "Guardando" : "Registrando") + " datos...</h4>"});
        f.submit();
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
  $("#btn-registrar-alumno").click(function () {
    $("input[name='registrarComoAlumno']").val("1");
    $("#formulario-interesado").submit();
  });
  $("#btn-guardar").click(function () {
    $("input[name='registrarComoAlumno']").val("0");
    $("#formulario-interesado").submit();
  });
}

var editorCargado = false;
function cargarFormularioCotizacion() {
  if ($("#descripcion-curso").length === 0) {
    return;
  }
  $("#formulario-interesado-cotizacion").validate({
    ignore: "#correo-cotizacion-prueba:not(:visible)",
    rules: {
      idCurso: {
        required: true
      },
      textoIntroductorio: {
        validarCkEditor: true
      },
      descripcionCurso: {
        validarCkEditor: true
      },
      modulos: {
        validarCkEditor: true
      },
      metodologia: {
        validarCkEditor: true
      },
      cursoIncluye: {
        validarCkEditor: true
      },
      inversion: {
        validarCkEditor: true
      },
      inversionCuotas: {
        validarCkEditor: true
      },
      notasAdicionales: {
        validarCkEditor: true
      },
      costoHoraClase: {
        required: true,
        validarDecimal: true
      },
      correoCotizacionPrueba: {
        required: true,
        email: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea enviar esta cotización?")) {
        $("#mod-correo-cotizacion-prueba").modal("hide");
        $.blockUI({message: "<h4>Enviando cotización...</h4>"});
        if ($("#correo-cotizacion-prueba").val() !== "") {
          var datos = procesarDatosFormulario(f);
          llamadaAjax($(f).attr("action"), "POST", datos, true,
              function (d) {
                $("body").unblock({
                  onUnblock: function () {
                    agregarMensaje("exitosos", "Cotización enviada.", true);
                  }
                });
              },
              function (d) {
              },
              function (de) {
                $("body").unblock({
                  onUnblock: function () {
                    agregarMensaje("errores", "Ocurrió un problema durante el envio de la cotización. Por favor inténtelo nuevamente.", true);
                  }
                });
              }
          );
        } else {
          f.submit();
        }
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
  $("#id-curso").change(function () {
    urlDatosCurso = (typeof (urlDatosCurso) === "undefined" ? "" : urlDatosCurso);
    urlBaseImagen = (typeof (urlBaseImagen) === "undefined" ? "" : urlBaseImagen);
    if (urlDatosCurso !== "" && urlBaseImagen !== "") {
      $.blockUI({message: "<h4>Cargando...</h4>"});
      llamadaAjax(urlDatosCurso.replace("/0", "/" + $(this).val()), "POST", {}, true,
          function (d) {
            var imagenCurso = (d.imagen !== null ? urlBaseImagen.replace(encodeURI("[RUTA_IMAGEN]"), d.imagen) : "");
            $("input[name='imagenCurso']").val(imagenCurso);
            $("#sec-imagen-curso").html(imagenCurso !== "" ? '<img src="' + imagenCurso + '" width="120"/>' : "");
            CKEDITOR.instances["descripcion-curso"].setData(d.descripcion);
            CKEDITOR.instances["modulos"].setData(d.modulos);
            CKEDITOR.instances["metodologia"].setData(d.metodologia);
            CKEDITOR.instances["curso-incluye"].setData(d.incluye);
            CKEDITOR.instances["inversion"].setData(d.inversion);
            CKEDITOR.instances["inversion-cuotas"].setData(d.inversionCuotas);
            CKEDITOR.instances["notas-adicionales"].setData(d.notasAdicionales);
            setTimeout(function () {
              agregarCamposCalculoInversionCuotas(true);
            }, 1000);
            $("body").unblock();
          }
      );
    }
  });
  CKEDITOR.replace("texto-introductorio");
  CKEDITOR.replace("descripcion-curso");
  CKEDITOR.replace("modulos");
  CKEDITOR.replace("metodologia");
  CKEDITOR.replace("curso-incluye");
  CKEDITOR.replace("inversion");
  CKEDITOR.replace("inversion-cuotas");
  CKEDITOR.replace("notas-adicionales");
  CKEDITOR.on("instanceReady", function (e) {
    if (!editorCargado) {
      editorCargado = true;
      $("#id-curso").trigger("change");
    }
  });
  $("#btn-envio-cotización").click(function () {
    $("#correo-cotizacion-prueba").val("");
    $("#formulario-interesado-cotizacion").submit();
  });
  $("#btn-envio-cotización-prueba").click(function () {
    var camposFormularioInteresadoCotizacion = $("#formulario-interesado-cotizacion").not("#correo-cotizacion-prueba:not(:visible)");
    if (!camposFormularioInteresadoCotizacion.valid()) {
      return false;
    }
    $("#mod-correo-cotizacion-prueba").modal("show");
  });
  incluirSeccionSubidaArchivos("adjuntos", {onSubmit: function () {
      return true;
    }, acceptFiles: "*", uploadStr: "Subir archivo"});
}
function copiarEnlaceFichaInscripcion(enlace) {
  window.prompt("Copiar enlace ficha de inscripción: Ctrl+C, Enter", enlace);
  return false;
}
