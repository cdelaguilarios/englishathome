window.addEventListener("load", verificarJqueryPago, false);
function verificarJqueryPago() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionPagos() : window.setTimeout(verificarJqueryPago, 100));
}
function cargarSeccionPagos() {
  motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
  urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);

  cargarListaPago();
  cargarFormularioPago();
  cargarFormularioActualizarPago();
  mostrarSeccionPago();

  //Común formularios
  $(".btn-cancelar-pago").click(function () {
    mostrarSeccionPago([1]);
  });

  //Común   
  registroHistorial = (typeof (registroHistorial) === "undefined" ? false : registroHistorial);
  if (obtenerParametroUrlXNombre("sec") === "pago" && !registroHistorial) {
    $("a[href='#pago']").trigger("click");
  }
  $("a[href='#pago']").click(function () {
    $(this).tab("show");
    $("#tab-lista-pagos").DataTable().responsive.recalc();
  });
}

//Lista
function cargarListaPago() {
  urlListarPagos = (typeof (urlListarPagos) === "undefined" ? "" : urlListarPagos);
  urlActualizarEstadoPago = (typeof (urlActualizarEstadoPago) === "undefined" ? "" : urlActualizarEstadoPago);
  urlEliminarPago = (typeof (urlEliminarPago) === "undefined" ? "" : urlEliminarPago);
  if (urlListarPagos !== "" && urlActualizarEstadoPago !== "" && urlEliminarPago !== "" && motivosPago !== "" && estadosPago !== "") {
    $("#tab-lista-pagos").DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: urlListarPagos,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[3, "desc"]],
      columns: [
        {data: "id", name: "pago.id", className: "text-center"},
        {data: "motivo", name: "pago.motivo", render: function (e, t, d, m) {
            return motivosPago[d.motivo];
          }},
        {data: "fecha", name: "pago.fecha", render: function (e, t, d, m) {
            return formatoFecha(d.fecha);
          }, className: "text-center", type: "fecha"},
        {data: "fechaRegistro", name: "pago.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center", type: "fecha"},
        {data: "estado", name: "pago.estado", render: function (e, t, d, m) {
            return '<div class="sec-btn-editar-estado-pago"><a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + d.id + '" data-idprofesor="' + d.idProfesor + '" data-estado="' + d.estado + '"><span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span></a></div>';
          }, className: "text-center"},
        {data: "monto", name: "pago.monto", render: function (e, t, d, m) {
            return 'S/. ' + redondear(d.monto, 2);
          }, className: "text-center", type: "monto"},
        {data: "id", name: "pago.id", orderable: false, searchable: false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="javascript:void(0);" onclick="editarPago(' + d.id + ');" title="Editar datos del pago"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar pago" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?\', \'tab-lista-pagos\', false, function(){recargarDatosTabla(\'tab-lista-clases\');reiniciarHistorial();})" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista-pagos");
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var montoTotal = 0, montoTotalPagina = 0;
        $('#tab-lista-pagos').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          montoTotal += parseFloat(i.monto);
        });
        $('#tab-lista-pagos').DataTable().rows({page: 'current'}).data().each(function (i) {
          montoTotalPagina += parseFloat(i.monto);
        });
        $(api.column(5).footer()).html("Total S/. " + redondear(montoTotal, 2) + (montoTotal !== montoTotalPagina ? "<br/>Total de la página S/." + redondear(montoTotalPagina, 2) : ""));
      }
    });
    $(window).click(function (e) {
      if (!$(e.target).closest('.sec-btn-editar-estado-pago').length) {
        $(".sec-btn-editar-estado-pago select").trigger("change");
      }
    });
    $(".btn-editar-estado-pago").live("click", function () {
      $("#sel-estados-pago").clone().val($(this).data("estado")).data("idpago", $(this).data("idpago")).data("idprofesor", $(this).data("idprofesor")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado-pago"));
      $(this).remove();
      event.stopPropagation();
    });
    $(".sec-btn-editar-estado-pago select").live("change", function () {
      var idpago = $(this).data("idpago");
      var idprofesor = $(this).data("idprofesor");
      if (urlActualizarEstadoPago !== "" && $(this).data("estado") !== $(this).val()) {
        llamadaAjax(urlActualizarEstadoPago, "POST", {"idPago": idpago, "idProfesor": idprofesor, "estado": $(this).val()}, true, undefined, undefined, function (de) {
          var rj = de.responseJSON;
          if (rj !== undefined && rj.mensaje !== undefined) {
            agregarMensaje("errores", rj.mensaje, true);
          } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
            agregarMensaje("errores", rj[Object.keys(rj)[0]][0], true);
          }
          $("#tab-lista-pagos").DataTable().ajax.reload();
        });
      }
      $(this).closest(".sec-btn-editar-estado-pago").append('<a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + idpago + '" data-idprofesor="' + idprofesor + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosPago[$(this).val()][1] + ' btn-estado">' + estadosPago[$(this).val()][0] + '</span></a>');
      $(this).remove();
    });
  }
}

//Formulario
function cargarFormularioPago() {
  $("#formulario-pago").validate({
    ignore: ":hidden",
    rules: {
      fecha: {
        required: true,
        validarFecha: true
      },
      imagenComprobante: {
        validarImagen: true
      },
      monto: {
        required: true,
        validarDecimal: true
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea registrar los datos de este pago?")) {
        $.blockUI({message: "<h4>Registrando datos...</h4>"});
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
  establecerCalendario("fecha-pago", false, false, false);

  $("#btn-nuevo-pago").click(function () {
    limpiarCamposPago();
    mostrarSeccionPago([2]);
  });
}

//Formulario editar
function cargarFormularioActualizarPago() {
  $("#formulario-actualizar-pago").validate({
    ignore: ":hidden",
    rules: {
      fecha: {
        required: true,
        validarFecha: true
      },
      imagenDocumentoVerificacion: {
        validarImagen: true
      },
      imagenComprobante: {
        validarImagen: true
      },
      monto: {
        required: true,
        validarDecimal: true
      }
    },
    submitHandler: function (f) {
      if ($("#sec-documentos-verificacion-lista").html() !== "" || $("#nombres-archivos-documentos-verificacion").val() !== "") {
        if (confirm("¿Está seguro que desea guardar los datos de este pago?")) {
          $.blockUI({message: "<h4>Guardando datos...</h4>"});
          f.submit();
        }
      } else {
        agregarMensaje("advertencias", "Debe subir la imagen de por lo menos una ficha de conformidad.", true, "#sec-mensajes-pago");
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
  establecerCalendario("fecha-actualizar-pago", false, false, false);
  incluirSeccionSubidaArchivos("documentos-verificacion", {onSubmit: function () {
      return true;
    }, acceptFiles: "image/*", uploadStr: "Subir archivo", maxFileCount: 20});
}
function editarPago(idPago) {
  obtenerDatosPago(idPago, function (d) {
    motivoPagoClases = (typeof (motivoPagoClases) === "undefined" ? "" : motivoPagoClases);
    if (urlArchivos !== "" && motivoPagoClases !== "") {
      limpiarCamposPago();
      $("#lista-motivo-actualizar-pago").val(d.motivo);
      $("#titulo-motivo-actualizar-pago").text($("#lista-motivo-actualizar-pago option:selected").text());
      $("#motivo-actualizar-pago").val(d.motivo);
      var datFecha = formatoFecha(d.fecha).split("/");
      $("#fecha-actualizar-pago").datepicker("setDate", (new Date(datFecha[1] + "/" + datFecha[0] + "/" + datFecha[2])));
      $("#estado-actualizar-pago").val(d.estado);
      $("#descripcion-actualizar-pago").val(d.descripcion);
      if (d.imagenesComprobante !== null && d.imagenesComprobante !== "") {
        var imagenes = d.imagenesComprobante.split(",");
        if (imagenes.length > 0) {
          if (imagenes[0] !== "") {
            var rutaImagen = urlArchivos.replace("/0", "/" + imagenes[0]);
            $("#imagen-comprobante-actualizar-pago").attr("href", rutaImagen);
            $("#imagen-comprobante-actualizar-pago").find("img").attr("src", rutaImagen);
          }
          for (var i = 1; i < imagenes.length; i++) {
            if (imagenes[i] !== "") {
              var datosImagen = imagenes[i].split(":");
              if (datosImagen.length === 2) {
                var rutaImagenDocumentoVerificacion = urlArchivos.replace("/0", "/" + datosImagen[0]);
                $("#sec-documentos-verificacion-lista").append('<div class="ajax-file-upload-container">' +
                    '<div class="ajax-file-upload-statusbar" style="width: 400px;">' +
                    '<div class="ajax-file-upload-filename">' +
                    '<a href="' + rutaImagenDocumentoVerificacion + '" target="_blank">' + datosImagen[1] + '</a>' +
                    '</div>' +
                    '<div class="ajax-file-upload-progress">' +
                    '<div class="ajax-file-upload-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '<div class="ajax-file-upload-red" onclick="eliminarDocumentoVerificacion(this, \'' + datosImagen[0] + '\')">Eliminar</div>' +
                    '</div>' +
                    '</div>');
              }
            }
          }
        }
      }
      if (d.motivo === motivoPagoClases) {
        $("#sec-documentos-verificacion-actualizar-pago").show();
      } else {
        $("#sec-documentos-verificacion-actualizar-pago").hide();
      }
      $("#monto-actualizar-pago").val(redondear(d.monto, 2));
      $("input[name='idPago']").val(d.id);
      mostrarSeccionPago([3]);
    }
  });
}
function eliminarDocumentoVerificacion(ele, nombreArchivo) {
  $("#nombres-archivos-documentos-verificacion-eliminados").val(nombreArchivo + "," + $("#nombres-archivos-documentos-verificacion-eliminados").val());
  $(ele).closest(".ajax-file-upload-container").remove();
}

//Datos
function verDatosPago(idPago) {
  if (motivosPago !== "" && urlArchivos !== "" && estadosPago !== "") {
    obtenerDatosPago(idPago, function (d) {
      $("#sec-descripcion-pago").hide();
      if (d.descripcion !== null && d.descripcion.trim() !== "") {
        $("#sec-descripcion-pago").show();
      }
      $("#dat-motivo-pago").text(motivosPago[d.motivo]);
      $("#dat-descripcion-pago").text(d.descripcion);
      $("#dat-monto-pago").html('S/. ' + redondear(d.monto, 2));
      $("#dat-estado-pago").html('<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span>');
      $("#dat-fecha-pago").text(formatoFecha(d.fecha));
      $("#dat-fecha-registro-pago").text(formatoFecha(d.fechaRegistro, true));
      if (d.imagenesComprobante !== null && d.imagenesComprobante !== "") {
        var imagenes = d.imagenesComprobante.split(",");
        if (imagenes[0] !== "") {
          var rutaImagen = urlArchivos.replace("/0", "/" + imagenes[0]);
          $("#dat-imagen-comprobante-pago").attr("href", rutaImagen);
          $("#dat-imagen-comprobante-pago").find("img").attr("src", rutaImagen);
        }
      }
      $("#mod-datos-pago").modal("show");
      $("body").unblock();
    });
  }
}

//Util
function limpiarCamposPago() {
  $("#formulario-pago, #formulario-actualizar-pago").find(":input, select").each(function (i, e) {
    if (e.name !== "fecha" && e.name !== "_token" && e.type !== "hidden") {
      if ($(e).is("select")) {
        $(e).prop("selectedIndex", 0);
      } else if ($(e).is(":checkbox")) {
        $(e).attr("checked", false);
        $(e).closest("label").removeClass("checked");
      } else {
        e.value = "";
      }
    }
  });
  $("#fecha-pago").datepicker("setDate", "today");
  $("form .help-block-error").remove();
}
function mostrarSeccionPago(numSecciones) {
  if (!numSecciones) {
    numSecciones = [1];
  }

  $("[id*='sec-pago']").hide();
  var auxSec = "";
  for (var i = 0; i < numSecciones.length; i++) {
    $("#sec-pago-" + auxSec + "" + numSecciones[i]).show();
    auxSec += "" + numSecciones[i];
  }
}
function obtenerDatosPago(idPago, funcionRetorno) {
  urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
  if (urlDatosPago !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosPago.replace("/0", "/" + idPago), "POST", {}, true,
        function (d) {
          if (funcionRetorno !== undefined)
            funcionRetorno(d);
          $("body").unblock();
        },
        function (d) {},
        function (de) {
          $('body').unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de datos del pago seleccionado. Por favor inténtelo nuevamente.", true, "#sec-mensajes-pago");
            }
          });
        }
    );
  }
}