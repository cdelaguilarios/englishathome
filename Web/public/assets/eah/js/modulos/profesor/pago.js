window.addEventListener("load", verificarJqueryPago, false);
function verificarJqueryPago() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionPagos() : window.setTimeout(verificarJqueryPago, 100));
}
function cargarSeccionPagos() {
  motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);

  cargarListaPago();
  cargarFormularioPago();
  mostrarSeccionPago();

  //Común   
  if (obtenerParametroUrlXNombre("sec") === "pago") {
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
      order: [[2, "desc"]],
      columns: [
        {data: "id", name: "pago.id", className: "text-center"},
        {data: "motivo", name: "pago.motivo", render: function (e, t, d, m) {
            return motivosPago[d.motivo];
          }},
        {data: "fechaRegistro", name: "pago.fechaRegistro", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
        {data: "estado", name: "pago.estado", render: function (e, t, d, m) {
            return '<div class="sec-btn-editar-estado-pago"><a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + d.id + '" data-idalumno="' + d.idAlumno + '" data-estado="' + d.estado + '"><span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span></a></div>';
          }, className: "text-center"},
        {data: "monto", name: "pago.monto", render: function (e, t, d, m) {
            return 'S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : '');
          }, className: "text-center"},
        {data: "id", name: "pago.id", orderable: false, searchable: false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="javascript:void(0);" onclick="verDatosPago(' + d.id + ');" title="Ver datos del pago"><i class="fa fa-eye"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar pago" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?\', \'tab-lista-pagos\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' +
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
        $(api.column(4).footer()).html("Total S/. " + redondear(montoTotal, 2) + (montoTotal !== montoTotalPagina ? "<br/>Total de la página S/." + redondear(montoTotalPagina, 2) : ""));
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
        llamadaAjax(urlActualizarEstadoPago, "POST", {"idPago": idpago, "idProfesor": idprofesor, "estado": $(this).val()}, true);
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
  $("#btn-nuevo-pago").click(function () {
    limpiarCamposPago();
    mostrarSeccionPago([2]);
  });
  $("#btn-cancelar-pago").click(function () {
    mostrarSeccionPago([1]);
  });
}
function limpiarCamposPago() {
  $("#formulario-pago input, #formulario-pago select").each(function (i, e) {
    if (e.name !== "_token" && e.type !== "hidden") {
      if ($(e).is("select")) {
        $(e).prop("selectedIndex", 0);
      } else {
        e.value = "";
      }
    }
  });
}

//Datos
function verDatosPago(idPago) {
  urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
  urlImagenes = (typeof (urlImagenes) === "undefined" ? "" : urlImagenes);
  if (urlDatosPago !== "" && motivosPago !== "" && urlImagenes !== "" && estadosPago !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosPago.replace("/0", "/" + idPago), "POST", {}, true,
        function (d) {
          $("#sec-descripcion-pago").hide();
          if (d.descripcion !== null && d.descripcion.trim() !== "") {
            $("#sec-descripcion-pago").show();
          }
          $("#dat-motivo-pago").text(motivosPago[d.motivo]);
          $("#dat-descripcion-pago").text(d.descripcion);
          $("#dat-monto-pago").html('S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : ''));
          $("#dat-estado-pago").html('<span class="label ' + estadosPago[d.estado][1] + ' btn-estado">' + estadosPago[d.estado][0] + '</span>');
          $("#dat-fecha-registro-pago").text(formatoFecha(d.fechaRegistro, true));
          if (d.imagenesComprobante !== null && d.imagenesComprobante !== "") {
            var imagenes = d.imagenesComprobante.split(",");
            if (imagenes[0] !== null && imagenes[0] !== "") {
              var rutaImagen = urlImagenes.replace("/0", "/" + imagenes[0]);
              $("#dat-imagen-comprobante-pago").attr("href", rutaImagen);
              $("#dat-imagen-comprobante-pago").find("img").attr("src", rutaImagen);
            }
          }
          $("#mod-datos-pago").modal("show");
          $("body").unblock();
        },
        function (d) {
        },
        function (de) {
          $("body").unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de datos del pago seleccionado. Por favor inténtelo nuevamente.", true, "#sec-mensajes-mod-datos-pago");
            }
          });
        }
    );
  }
}

//Util
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