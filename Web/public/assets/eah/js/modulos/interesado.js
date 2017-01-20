var editorCargado = false;
$(document).ready(function () {
  cargarLista();
  cargarFormulario();
  cargarFormularioCotizacion();
});

function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlActualizarEstado = (typeof (urlActualizarEstado) === "undefined" ? "" : urlActualizarEstado);
  urlCotizar = (typeof (urlCotizar) === "undefined" ? "" : urlCotizar);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  if (urlListar !== "" && urlEditar !== "" && urlCotizar !== "" && urlEliminar !== "" && estados !== "") {
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
      columns: [
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "");
          }},
        {data: "telefono", name: "entidad.telefono"},
        {data: "correoElectronico", name: "entidad.correoElectronico"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            return '<div class="sec-btn-editar-estado"><a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span></a></div>';
          }, className:"text-center"},
        {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="' + (urlCotizar.replace("/0", "/" + d.id)) + '" title="Enviar cotización"><i class="fa fa-dollar"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar interesado" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta persona interesada?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className:"text-center"}
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
      cursoInteres: {
        required: true
      }
    },
    submitHandler: function (f) {
      if (confirm($("#btn-guardar").text().trim() === "Guardar"
          ? "¿Está seguro que desea guardar los cambios de los datos de la persona interesada?"
          : "¿Está seguro que desea registrar los datos de esta persona interesada?")) {
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
    }
  });
}

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
      descripcionCurso: {
        required: function ()
        {
          CKEDITOR.instances["descripcion-curso"].updateElement();
        }
      },
      metodologia: {
        required: function ()
        {
          CKEDITOR.instances["metodologia"].updateElement();
        }
      },
      cursoIncluye: {
        required: function ()
        {
          CKEDITOR.instances["curso-incluye"].updateElement();
        }
      },
      inversion: {
        required: function ()
        {
          CKEDITOR.instances["inversion"].updateElement();
        }
      },
      inversionCuotas: {
        required: function ()
        {
          CKEDITOR.instances["inversion-cuotas"].updateElement();
        }
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
          var datos = {};
          var fDatos = $(f).serializeArray();
          $(fDatos).each(function (i, o) {
            datos[o.name] = o.value;
          });
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
    }
  });
  $("#id-curso").change(function () {
    urlDatosCurso = (typeof (urlDatosCurso) === "undefined" ? "" : urlDatosCurso);
    if (urlDatosCurso !== "") {
      $.blockUI({message: "<h4>Cargando...</h4>"});
      llamadaAjax(urlDatosCurso.replace("/0", "/" + $(this).val()), "POST", {}, true,
          function (d) {
            CKEDITOR.instances["descripcion-curso"].setData(d.descripcion);
            CKEDITOR.instances["metodologia"].setData(d.metodologia);
            CKEDITOR.instances["curso-incluye"].setData(d.incluye);
            CKEDITOR.instances["inversion"].setData(d.inversion);
            CKEDITOR.instances["inversion-cuotas"].setData(d.inversionCuotas);
            $("body").unblock();
          }
      );
    }
  });
  CKEDITOR.replace("descripcion-curso");
  CKEDITOR.replace("metodologia");
  CKEDITOR.replace("curso-incluye");
  CKEDITOR.replace("inversion");
  CKEDITOR.replace("inversion-cuotas");
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
}
